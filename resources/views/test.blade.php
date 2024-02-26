<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- <form method="post" action="{{url('/testing')}}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file"/>
        <input type="submit" name="submit" value="Submit" />
    </form> -->
    <form method="get" action="{{url('/test2')}}">
    <input type="checkbox" name="check">
    <input type="submit" name="submit"/>
    </form>
</body>
</html>