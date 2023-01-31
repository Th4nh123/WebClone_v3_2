@extends('admin.layout_admin.layout_admin')
@section("main")

    <div class="card" style="max-height: 100vh">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-sm-6 col-md-4 mb-3 mb-sm-0 d-flex">
                    <div id="reportrange"
                         style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                        <i class="tio-date-range"></i>&nbsp;
                        <span></span> <i class="tio-chevron-down"></i>
                    </div>
                    <button class="btn btn-primary pt-1 pb-1 pr-2 pl-2 ml-2" id="loc">Lọc</button>
                </div>
                <div class="col-sm-6 col-md-4 mb-3 mb-sm-0 d-flex justify-content-end">

                    <a href="https://rdone.net/admin/them-bai-viet" class="btn btn-primary pt-1 pb-1 pr-2 pl-2">Thêm
                        mới</a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->

        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <div id="datatable_wrapper" class="dataTables_wrapper no-footer">

                <table id="datatable"
                       class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer"
                       role="grid" aria-describedby="datatable_info">
                    <thead class="thead-light">
                    <tr role="row">

                        <th class="table-column-pl-0 sorting text-center p-1 align-middle" tabindex="0"
                            aria-controls="datatable" rowspan="1"
                            colspan="1" aria-label="Name: activate to sort column ascending">
                            STT
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Position: activate to sort column ascending">Ảnh
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Country: activate to sort column ascending">Tên bài
                            viết
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Status: activate to sort column ascending">Tác giả
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Portfolio: activate to sort column ascending">
                            Lượt xem
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Role: activate to sort column ascending">Ngày tạo
                        </th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label=""
                        >Hành động
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($ds_bai_viet as $item)
                        <tr role="row" class="odd">


                            <td class="table-column-pl-0 text-center">
                                @if(empty(Request::get('page')))
                                    {{$loop->index + 1+ Request::get('page')* count($ds_bai_viet)}}
                                @else
                                    {{$loop->index + 1 + Request::get('page')* count($ds_bai_viet) -15}}
                                @endif
                            </td>
                            <td>
                                {{--                                <img src="{{asset('storage/images/'.$item->twitter_image)}}" alt="" style="width: 80px;height: 80px">--}}
                                {{--                                <img src="{{ $item->twitter_image }}" alt="" style="width: 80px;height: 80px">--}}
                                @if(preg_match_all('((http|https)\:\/\/)',$item->twitter_image))
                                    <img src="{{ $item->twitter_image }}" alt="" style="width: 80px;height: 80px">
                                @else
                                    <img src="{{asset('images/'.$item->twitter_image)}}" alt=""
                                         style="width: 80px;height: 80px">
                                @endif

                            </td>

                            <td><a href="{{route('suaBV',['id'=>$item->ID])}}">{{ $item->post_title }}</a></td>

                            <td> {{ $item->display_name }}
                            @if($str_start == $str_end)
                                @if(!empty(json_decode($item->post_view,true)[$str_start]))
                                    <td class="text-center">{{json_decode($item->post_view,true)[$str_start]}}</td>
                                @else
                                    <td class="text-center">0</td>
                                @endif
                            @else
                                <?php
                                $count = 0;
                                $m_start = date('m', strtotime(str_replace('/', '-', Request::get('start'))));
                                $m_end = date('m', strtotime(str_replace('/', '-', Request::get('end'))));
                                for ($i = 0; $i <= (int)$m_end - (int)$m_start; $i++) {
                                    $tg = date('y-m', strtotime("+" . $i . " months", strtotime(str_replace('/', '-', Request::get('start')))));
                                    if (!empty(json_decode($item->post_view, true)[$tg])) {
                                        $count += (int)json_decode($item->post_view, true)[$tg];
                                    }
                                }
                                echo $count;
                                ?>
                                <td class="text-center">{{$count}}</td>
                            @endif
                            <td>{{$item->post_date}}</td>
                            <td>
                                @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')
                                    <a class="btn btn-sm btn-white" href="{{Request::root()."/".$item->post_name}}">
                                        <i class="tio-edit"></i> View
                                    </a>
                                @endif
                                @if(session()->get('role')[0] == 'admin')
                                    <a class="btn btn-sm btn-white" href="{{route('xoaBV',['id'=>$item->ID])}}"
                                       onclick="return confirm('Bạn có chắc không?')">
                                        Delete
                                    </a>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">Showing 1 to 15 of 24
                    entries
                </div>
            </div>
        </div>
        <!-- End Table -->


    </div>
    <script type="text/javascript">
        let URL_Base = window.location.origin
        let START = moment();
        let END = moment();
        $("#loc").click(function () {
            console.log(START.format('MM/DD/YYYY'))
            window.location.href = URL_Base + '/admin/thong-ke-view-bai-viet/?start='
                + START.format('DD/MM/YYYY') + '&end=' + END.format('DD/MM/YYYY')
        })
        $(function () {
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                START = start;
                END = end
            }

            $('#reportrange').daterangepicker({
                startDate: START,
                endDate: END,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(START, END);

        });
    </script>
@endsection
