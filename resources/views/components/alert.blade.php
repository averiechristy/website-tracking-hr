@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
          <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
        <strong>{!! $message !!}</strong>

      </div>
@endif

@if ($message = Session::get('erroruser'))
<div class="alert alert-danger alert-block">
        <!-- <button type="button" class="close" data-dismiss="alert"></button>	 -->
        <strong>{!! $message !!}</strong>

      </div>
@endif

@if ($message = Session::get('successdua'))
<div class="alert alert-success alert-block">
       
          <strong>{{ $message }}</strong>
      </div>
@endif