<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    {{--    <link rel="icon" href="%PUBLIC_URL%/favicon.ico"/>--}}
    <link href="https://rdsic.edu.vn/webroot/favicon.ico" type="image/x-icon" rel="icon">
    <link href="https://rdsic.edu.vn/webroot/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#000000"/>
    <meta
        name="description"
        content="Web site created using create-react-app"
    />

    <link rel="apple-touch-icon" href="%PUBLIC_URL%/logo192.png"/>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap"
        rel="stylesheet"
    />
    <link rel="manifest" href="%PUBLIC_URL%/manifest.json"/>

    <link
        rel="stylesheet"
        href="{{Request::root().'/css/css_admin/theme.min.css'}}"
    />
    <link
        rel="stylesheet"
        href="{{Request::root().'/css/css_admin/vendor.min.css'}}"
    />
    <link
        rel="stylesheet"
        href="{{Request::root().'/js/libs_admin/icon-set/style.css'}}"/>
    <title>Quản trị rdone</title>

</head>
<body>
<div class="col-8 m-auto">
    <h3 class="text-center mt-3 mb-3">Danh Sách Link</h3>
    <table id="datatable"
           class="table  table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer">
        <thead class="thead-light">
        <tr role="row">
            <th class="table-column-pl-0 sorting_disabled" rowspan="1" colspan="1">
                ID
            </th>
            <th class="table-column-pl-0 sorting_disabled" rowspan="1" colspan="1">
                Name
            </th>
            <th class="table-column-pl-0 sorting_disabled" rowspan="1" colspan="1">
                Value
            </th>
            <th class="table-column-pl-0 sorting_disabled" rowspan="1" colspan="1">
                Link
            </th>
        </tr>
        </thead>

        <tbody>
        @if(count($rs))
            @foreach($rs as $item)
                <tr role="row" class="odd item-link">
                    <td class="table-column-pl-0">{{$item->id}}</td>
                    <td class="table-column-pl-0">{{$item->keyHD}}</td>
                    <td class="table-column-pl-0">{{$item->valueHD}}</td>
                    <td class="table-column-pl-0">{{$item->list_link}}</td>
                </tr>
            @endforeach
        @else
            <tr role="row" class="odd">
                <td class="table-column-pl-0" colspan="4">Chưa có dữ liệu, bạn chưa thêm key</td>
            </tr>
        @endif
        </tbody>
    </table>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    console.log(window.location.href)
    let html = document.getElementsByClassName('item-link')
    let list = []
    for (let i = 0; i < html.length; i++) {
        list.push({
            key: html[i].childNodes[3].textContent,
            value: html[i].childNodes[5].textContent,
            list_link: html[i].childNodes[7].textContent.split("@@")
        })
    }
    console.log(list)
    const random = Math.floor(Math.random() * list.length);
    let rs = list[random]
    const index_link_random = Math.floor(Math.random() * rs.list_link.length);
    let link = rs.list_link[index_link_random]
    if (window.location.href.includes(".html")) {
        window.location.href = link
    }
</script>


</body>
</html>
