<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post" action="{{url('/testing')}}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file"/>
        <input type="submit" name="submit" value="Submit" />
    </form>
</body>
</html>