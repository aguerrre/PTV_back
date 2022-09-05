<!DOCTYPE html>
<html>

<head>
    <title>Informe de formulario</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <td>
                    <img src="./images/logo.png" width="100px">
                </td>
            </tr>
        </thead>
        <hr/>
        <tbody>
            <table>
                <thead>
                    <tr>
                        <h1>{{ $title }}</h1>
                    </tr>
                </thead>
                <hr>
                <tbody>
                    <tr>
                        <td>
                            <p>Nombre: {{ $name }}</p>
                        </td>
                        <td>
                            <p>Email: {{ $email }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Firma: </p>
                        </td>
                    </tr>
                    <tr>
                        <td><img src="{{ $img }}"></td>
                    </tr>
                </tbody>
            </table>
        </tbody>
    </table>



</body>

</html>
