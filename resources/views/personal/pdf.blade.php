<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Personal</title>

    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th,td{
            border:1px solid #ccc;
            padding:6px;
            text-align:left;
        }

        th{
            background:#f2f2f2;
        }

        .titulo{
            text-align:center;
            font-size:18px;
            margin-bottom:10px;
        }

        .sub{
            text-align:center;
            margin-bottom:20px;
            color:#666;
        }
    </style>
</head>

<body>

<div class="titulo">
    LISTADO DE PERSONAL CMAN VALVES SOLUTIONS
</div>

<div class="sub">
    Generado: {{ $fechaGeneracion->format('d/m/Y H:i') }}
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>División</th>
            <th>Área</th>
            <th>Departamento</th>
            <th>Puesto</th>
            <th>Sexo</th>
            <th>Estatus</th>
            <th>Sueldo</th>
        </tr>
    </thead>

    <tbody>
        @foreach($personal as $p)
        <tr>
            <td>{{ $p->employee_id }}</td>
            <td>{{ $p->nombre_completo }}</td>
            <td>{{ $p->division }}</td>
            <td>{{ $p->area }}</td>
            <td>{{ $p->departamento }}</td>
            <td>{{ $p->grado }}</td>
            <td>{{ $p->sexo }}</td>
            <td>{{ $p->estatus }}</td>
            <td>
                @if($p->sueldo)
                    ${{ number_format($p->sueldo,2) }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <td><strong>Total Activos:</strong> {{ $totalActivo }}</td>
        <td><strong>Total Bajas:</strong> {{ $totalBaja }}</td>
        <td><strong>Total Sueldos:</strong> ${{ number_format($totalSueldos,2) }}</td>
    </tr>
</table>

</body>
</html>
