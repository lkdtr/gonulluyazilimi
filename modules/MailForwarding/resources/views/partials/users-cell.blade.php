<td>{{ $user->activeEmailRedirects->pluck('email_alias')->implode(', ') }}</td>
