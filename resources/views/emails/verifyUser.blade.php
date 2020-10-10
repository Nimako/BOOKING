<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Verify Account</title>
  </head>
  <body>
    <div class="container">

        <h2>Verify your email address {{$user['name']}}</h2>
        <br/>
        Your registered email ID is {{$user['email']}} , Please click on the below link to verify your email account
        <br/>
        <a class="btn btn-block btn-primary" href="{{url('VerifyUser', $user['token'])}}">Confirm</a>

    </div>
  </body>
</html>