
@extends('student.navigation')
 
@section('content')

<style type="text/css">
    .mw-50{
        min-width: 50%;
    }
</style>
<div class="online-course">
    <div class="row">
       @foreach ($courses as $list)
       <?php
          $lists =App\Models\Subject::get()->where('id',$list->subject_id)->first(); 
          $data = App\Models\User::get()->where('id', $list->user_id)->first(); 
          $info = json_decode($data->user_information);
          $user_image = $info->photo;
            if(!($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
       ?>
        <div class="col-lg-3">
           <div class="list-card">
            <div class="card">
                    <div class="card-head">
                    @if(File::exists('assets/uploads/course_thumbnails/'.$list->thumbnail))
                        <img src="{{ asset('assets/uploads/course_thumbnails/'.$list->thumbnail ) }}" alt="thumbnails">
                        @else
                            <img src="{{ asset('assets/uploads/course_thumbnails/course-thumbnail.png' ) }}" alt="thumbnails">
                        
                        @endif
                        <span class="badge badge-control badge-success float-right mt-2 mr-2 p-1">{{$lists->name}}</span>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">{{ $list->title}}</h4>
                        <div class="info-title">
                             <img src="{{ asset('assets/') }}/{{ $user_image }}"  alt="">
                            <div class="media d-flex align-items-center w-100 ">
                                <h5>{{ $data->name }}</h5>
                            </div>
                        </div>
                        <div class="w-100 text-center btn-control">
                            <a href="{{ route('student.addons.courses.course_preview',['id'=>$list->id ,'course_id' => $list->id]) }}" class="btn btn-secondary mw-50">{{ get_phrase('Continue lesson ') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


@endsection