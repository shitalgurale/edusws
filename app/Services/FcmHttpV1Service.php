<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Enrollment;

class FcmHttpV1Service
{
    protected $credentials;
    protected $projectId;

    public function __construct()
    {
        $path = storage_path('app/firebase/firebase-service-account.json');
        $this->credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $path
        );

        $json = json_decode(file_get_contents($path), true);
        $this->projectId = $json['project_id'];
    }

    public function sendToAllUsers(string $message)
    {
        $users = User::all();
        return $this->sendToUsers($users, $message, 'All Users');
    }

    public function sendToAllTeachers(string $message)
    {
        $users = User::where('role_id', 3)->get(); // Teachers
        return $this->sendToUsers($users, $message, 'All Teachers');
    }

    public function sendToAllParents(string $message)
    {
        $users = User::where('role_id', 6)->get(); // Parents
        return $this->sendToUsers($users, $message, 'All Parents');
    }

    public function sendToAllStudents(string $message)
    {
        $users = User::where('role_id', 7)->get(); // Students
        return $this->sendToUsers($users, $message, 'All Students');
    }

    public function sendToParentsOfClass(int $classId, string $message)
    {
        $studentIds = Enrollment::where('class_id', $classId)->pluck('user_id');
        $parentIds = User::whereIn('id', $studentIds)->pluck('parent_id')->filter()->unique();
        $parents = User::whereIn('id', $parentIds)->get();

        return $this->sendToUsers($parents, $message, "Parents of Class ID $classId");
    }

    public function sendToParentsOfSection(int $classId, int $sectionId, string $message)
    {
        $studentIds = Enrollment::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->pluck('user_id');
        $parentIds = User::whereIn('id', $studentIds)->pluck('parent_id')->filter()->unique();
        $parents = User::whereIn('id', $parentIds)->get();

        return $this->sendToUsers($parents, $message, "Parents of Class ID $classId Section ID $sectionId");
    }

    public function sendToSpecificUsers(array $tokens, string $title, string $body)
    {
        $accessToken = $this->credentials->fetchAuthToken()['access_token'];
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $success = 0;
        $failed = 0;

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ]
            ];

            try {
                $response = Http::withToken($accessToken)
                    ->post($url, $payload);

                if ($response->successful()) {
                    Log::info("✅ Sent to token: $token");
                    $success++;
                } else {
                    Log::error("❌ FCM failed for token: $token | Response: " . $response->body());
                    $failed++;
                }
            } catch (\Throwable $e) {
                Log::error("❌ Exception for token: $token | Error: " . $e->getMessage());
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'total_users' => count($tokens),
        ];
    }

    private function sendToUsers($users, string $message, string $label)
    {
        $accessToken = $this->credentials->fetchAuthToken()['access_token'];
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            $tokens = collect([
                $user->fcm_token1,
                $user->fcm_token2,
                $user->fcm_token3,
                $user->fcm_token4,
                $user->fcm_token5,
            ])->filter()->unique();

            foreach ($tokens as $token) {
                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => "EduSWS Notification",
                            'body' => $message,
                        ],
                        'data' => [
                            'user_id' => (string) $user->id,
                            'target' => $label,
                        ],
                        'android' => [
                            'priority' => 'high',
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ]
                ];

                try {
                    $response = Http::withToken($accessToken)
                        ->post($url, $payload);

                    if ($response->successful()) {
                        Log::info("✅ Sent to {$user->email} | Token: $token");
                        $sent++;
                    } else {
                        Log::error("❌ FCM failed for {$user->email} | Token: $token | Response: " . $response->body());
                        $failed++;
                    }
                } catch (\Throwable $e) {
                    Log::error("❌ Exception for {$user->email} | Token: $token | Error: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        return [
            'success' => $sent,
            'failed' => $failed,
            'total_users' => $users->count(),
        ];
    }
}
