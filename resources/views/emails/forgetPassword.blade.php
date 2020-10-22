<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Reset password instructions</title>
    <style>
    .btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
    }
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        transition: color .15s ease-in-out,
        background-color .15s ease-in-out,
        border-color .15s ease-in-out,
        box-shadow .15s ease-in-out;
    }

    </style>
  </head>
  <body>
    <div class="container">

      <h2>Hello <a href="mailto:{{$user['email']}}">{{$user['email']}}</a></h2>

      <p>Someone has requested a link to change your password. You can do this through the button below.</p>

      <center>
        <a class="btn btn-block btn-primary" href="{{url('resetpassword', $user['token'])}}">Change my password</a>
      </center>

    </div>
  </body>
</html>