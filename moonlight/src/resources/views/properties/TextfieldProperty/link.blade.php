@if ($isMainProperty)
<div class="label mainp"><i class="fa fa-flag"></i><span>ID или название</span></div>
@else
<div class="label textfield"><i class="fa fa-pencil"></i><span>{{ $title }}</span></div>
@endif