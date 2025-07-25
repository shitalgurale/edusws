<!--Main Jquery-->
<script src="{{ asset('assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
  <!--Bootstrap bundle with popper-->
  <script src="{{ asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
  <!-- Datepicker js -->
  <script src="{{ asset('assets/js/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
  <!-- Select2 js -->
  <script src="{{ asset('assets/js/select2.min.js') }}"></script>

  <!--Custom Script-->
  <script src="{{ asset('assets/js/script.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- Calender js -->
  <script src="{{ asset('assets/calender/main.js') }}"></script>
  <script src="{{ asset('assets/calender/locales-all.js') }}"></script>

  <!--Toaster Script-->
  <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

  <!--pdf Script-->
  <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
  <script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>
  <!-- Player Js -->
  <script src="{{ asset('assets/js/plyr.js') }}"></script>

  <!--html2canvas Script-->
  <script src="{{ asset('assets/js/html2canvas.min.js') }}"></script>
  <script>const player = new Plyr('#player');</script>
  <script>

    "use strict";
    
		@if(Session::has('message'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.success("{{ session('message') }}");
		@endif

		@if(Session::has('error'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.error("{{ session('error') }}");
		@endif

		@if(Session::has('info'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.info("{{ session('info') }}");
		@endif

		@if(Session::has('warning'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.warning("{{ session('warning') }}");
		@endif
	</script>

	<script>

    "use strict";

    jQuery(document).ready(function(){
      $('input[name="datetimes"]').daterangepicker({
          timePicker: true,
          startDate: moment().startOf('day').subtract(30, 'day'),
          endDate: moment().startOf('day'),
          locale: {
         format: 'M/DD/YYYY '
        }

      });
    });

    </script>

</body>
</html>
