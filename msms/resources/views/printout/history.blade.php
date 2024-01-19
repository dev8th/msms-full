<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="icon" type="image/x-icon" href="https://app-mismass.com/assets/dist/pic/favicon.ico">
    <title>Detail History</title>
    
    <style>
        body {
            font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
            font-size: 14px;
        }
    </style>
</head>
<body>
    @foreach($history as $h)
    <table>
        <tr>
            <td style='vertical-align:top'>User</td>
            <td style='padding:0px 20px 0px 20px'><b>{{$h->created_by}}</b><br><b>{{App\Http\Controllers\Controller::dateFormatIndo($h->created_at,2)}}</b></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td style='vertical-align:top'>Aktivitas</td>
            <td style='padding:0px 20px 0px 20px'><?php echo $h->description ?></td>
        </tr>
    </table>
    @endforeach
</body>
</html>