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
        
        <h2>Verify your email address</h2>

       <p>
        You've created an account with the email address: {{$user['email']}}
        Click 'confirm' to verify the email address and unlock your full account.
        We'll also import any bookings you've made with that address.
       </p>

        {{-- <a class="btn btn-block btn-primary" href="{{url('VerifyUser', $user['token'])}}">Confirm</a> --}}

        <a class="btn btn-block btn-primary" href="https://listing-site-df269.firebaseapp.com/verify-email/{{$user['token']}}">Confirm</a>

    </div>
  </body>
</html>