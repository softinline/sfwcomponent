@if(empty($counter))
    @php
        $counter = 1
    @endphp
@endif
@for ($i = 1; $i <= $counter; $i++)
    <div class="modal fade" tabindex="-1" role="dialog" id="modal{{ $i }}" @if($i > 1) data-backdrop="0" @endif >
        <div class="modal-dialog @if($i == 1) modal-lg @endif">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endfor