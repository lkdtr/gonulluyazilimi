<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=640">
        <title>Hoş Geldiniz</title>
    </head>
    <body style="color:#000000;font-size:18px;font-family:Verdana, Geneva, sans-serif;background:#ffffff;">

    <div class="container" style="padding: 1rem; background: #f5f5f5;">
        <p>
            <a href="{{ config('app.url') }}" style="text-decoration: none; color: #004153; font-weight: bold;">
                <table>
                    <tbody>
                        <tr>
                            @if ($organization->logoUrl(true))
                                <td>
                                    <img src="{{ $organization->logoUrl(true) }}" alt="{{ $organization->name() }}" style="height: 50px;">
                                </td>
                            @endif
                            <td>
                                <span style="font-size: 20px;"> {{ $organization->name() }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </a>
        </p>
        <p>Sayın {{$data->name." ".$data->surname}}</p>
        <p>
            {{ $organization->name() }} portalına hoş geldiniz.
            @if ($organization->get('contact_email'))
                Yorum, eleştiri ve önerilerinizi <a href="mailto:{{ $organization->get('contact_email') }}">{{ $organization->get('contact_email') }}</a> adresine gönderebilirsiniz.
            @endif
        </p>
    </div>

    </body>
</html>
