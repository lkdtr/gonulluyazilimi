<p>Merhaba {{ $name }},</p>

<p>{{ $organization->name() }} portalında kaydınız var; hesabınızı etkinleştirmek için aşağıdaki bağlantıdan parolanızı belirleyin:</p>

<p><a href="{{ $link }}">{{ $link }}</a></p>

<p>Bağlantı {{ $hours }} saat geçerlidir. Bu isteği siz yapmadıysanız bu e-postayı dikkate almayın.</p>

<p>{{ $organization->name() }}</p>
