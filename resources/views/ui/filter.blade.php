<form action="{{route('requests.index')}}" method="GET">
    <div>
        <div class="row">
            <div class="form-group col-4">
                <div class="custom-control custom-radio">
                    <input name="is_checked" type="radio" class="custom-control-input" id="old"
                           value="0" @if(request()->is_checked === "0") checked @endif>
                    <label class="custom-control-label" for="old">Просмотренные</label>
                </div>
                <div class="custom-control custom-radio">
                    <input name="is_checked" type="radio" class="custom-control-input" id="new"
                           value="1" @if(request()->is_checked === "1") checked @endif>
                    <label class="custom-control-label" for="new">Непросмотренные</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-4">
                <div class="custom-control custom-radio">
                    <input name="status" type="radio" class="custom-control-input" id="opened"
                           value="opened" @if(request()->status === "opened") checked @endif>
                    <label class="custom-control-label" for="opened">Открытые</label>
                </div>
                <div class="custom-control custom-radio">
                    <input name="status" type="radio" class="custom-control-input" id="closed"
                           value="closed" @if(request()->status === "closed") checked @endif>
                    <label class="custom-control-label" for="closed">Закрытые</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-4">
                <div class="custom-control custom-radio">
                    <input name="resp" type="radio" class="custom-control-input" id="withResponse"
                           value="1" @if(request()->resp === "1") checked @endif>
                    <label class="custom-control-label" for="withResponse">С ответом</label>
                </div>
                <div class="custom-control custom-radio">
                    <input name="resp" type="radio" class="custom-control-input" id="withoutResponse"
                           value="0" @if(request()->resp === "0") checked @endif>
                    <label class="custom-control-label" for="withoutResponse">Без ответа</label>
                </div>
            </div>
        </div>
        <div class="row py-1">
            <div class="col-4 btn-group special" role="group">
                <button class="col btn btn-dark" type="submit">Сбросить</button>
                <button class="col btn btn-outline-dark" type="submit">Найти</button>
            </div>
        </div>
    </div>
</form>
