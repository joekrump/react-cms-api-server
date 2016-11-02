<h2>Password Reset Link for React CMS</h2>
<p>
  Click here to reset your password: <a href="{{ $link = 'http://localhost:3000/reset-password?_t='.$token.'&email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
</p>
