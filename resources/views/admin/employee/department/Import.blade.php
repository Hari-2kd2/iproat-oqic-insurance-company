<div class="bg-title border" style="margin: 12px;padding:12px">
    <div class="border col-sm-12 col-md-12">
        <div class="border" style="margin-left: 12px;margin-right: 12px">
            <form action="{{ route('employee.import') }}" method="post"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <p class="container border"><span><i class="fa fa-upload"></i></span><span
                        style="margin-left: 8px"> Upload Employee Excel
                        File Here</span></p>
                <div class="row">
                    <div class="col-md-6" style="margin-left: 46px;  margin-bottom: 2px;">
                        <input type="file" name="select_file" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <input class="btn btn-success" style="margin-top: 2px;width:120px"
                            type="submit" value="Upload">
                    </div>
                    <div class="text-right">
                        @php
                            $path = 'app\public\templates\employee_details.xlsx';
                        @endphp
                        <a href="{{ route('employee.downloadFile') }}">
                            <input type="button" id="template1" class="btn btn-info template1"
                                value="Sample Format" type="submit"
                                style="margin-left: 12px;margin-top: 2px;width:184px" />
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>