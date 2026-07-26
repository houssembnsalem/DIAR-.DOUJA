<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1e293b; }
.page { padding:25px; }
.header { display:table; width:100%; border-bottom:2px solid #2563eb; padding-bottom:15px; margin-bottom:20px; }
.header-left { display:table-cell; vertical-align:top; }
.header-right { display:table-cell; vertical-align:top; text-align:right; }
h1 { color:#2563eb; font-size:20px; margin-bottom:3px; }
.period { font-size:13px; color:#64748b; }
.stats-grid { display:table; width:100%; margin-bottom:20px; }
.stat-cell { display:table-cell; width:16.66%; padding:0 5px; text-align:center; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 5px; }
.stat-value { font-size:16px; font-weight:bold; color:#2563eb; }
.stat-label { font-size:9px; color:#64748b; margin-top:2px; }
h2 { font-size:14px; font-weight:bold; border-left:3px solid #2563eb; padding-left:8px; margin-bottom:10px; margin-top:20px; }
table { width:100%; border-collapse:collapse; }
table th { background:#1e293b; color:#fff; padding:6px 10px; text-align:left; font-size:10px; }
table th.right { text-align:right; }
table td { padding:6px 10px; border-bottom:1px solid #e2e8f0; font-size:10px; }
table td.right { text-align:right; }
table tr:last-child td { border-bottom:none; }
.profit { color:#16a34a; font-weight:bold; }
.expense { color:#dc2626; }
.footer { margin-top:20px; border-top:1px solid #e2e8f0; padding-top:10px; color:#94a3b8; font-size:9px; text-align:center; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('img/logo.png') }}" style="height:60px; margin-bottom:10px;">
            <div class="period">Période : {{ $period }} | Généré le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
        <div class="header-right">
            <div style="font-size:24px; font-weight:bold; color:#2563eb;">{{ number_format($stats['profit'],0,'.',',') }} DT</div>
            <div style="color:#64748b; font-size:10px;">Bénéfice net</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-cell"><div class="stat-box"><div class="stat-value">{{ number_format($stats['revenue'],0,'.',',') }}</div><div class="stat-label">Revenus (DT)</div></div></div>
        <div class="stat-cell"><div class="stat-box"><div class="stat-value" style="color:#dc2626;">{{ number_format($stats['expenses'],0,'.',',') }}</div><div class="stat-label">Dépenses (DT)</div></div></div>
        <div class="stat-cell"><div class="stat-box"><div class="stat-value" style="color:#16a34a;">{{ number_format($stats['profit'],0,'.',',') }}</div><div class="stat-label">Bénéfice (DT)</div></div></div>
        <div class="stat-cell"><div class="stat-box"><div class="stat-value">{{ $stats['total_reservations'] }}</div><div class="stat-label">Réservations</div></div></div>
        <div class="stat-cell"><div class="stat-box"><div class="stat-value">{{ $stats['occupancy_rate'] }}%</div><div class="stat-label">Occupation</div></div></div>
        <div class="stat-cell"><div class="stat-box"><div class="stat-value">{{ $stats['total_nights'] }}</div><div class="stat-label">Nuits louées</div></div></div>
    </div>

    <h2>Performance par logement</h2>
    <table>
        <thead><tr><th>Logement</th><th>Type</th><th class="right">Réservations</th><th class="right">Nuits</th><th class="right">Occupation</th><th class="right">Revenus (DT)</th></tr></thead>
        <tbody>
            @foreach($stats['properties_stats'] as $ps)
            <tr>
                <td>{{ $ps['name'] }}</td>
                <td>{{ $ps['type'] }}</td>
                <td class="right">{{ $ps['reservations'] }}</td>
                <td class="right">{{ $ps['nights'] }}</td>
                <td class="right">{{ $ps['occupancy'] }}%</td>
                <td class="right profit">{{ number_format($ps['revenue'],0,'.',',') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($stats['top_properties']))
    <h2>Top logements les plus rentables</h2>
    <table>
        <thead><tr><th>Rang</th><th>Logement</th><th class="right">Revenus (DT)</th></tr></thead>
        <tbody>
            @foreach($stats['top_properties'] as $i => $prop)
            <tr><td>{{ $i+1 }}</td><td>{{ $prop['name'] }}</td><td class="right profit">{{ number_format($prop['revenue'],0,'.',',') }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">Rapport généré automatiquement par DIAR DOUJA — {{ now()->format('d/m/Y H:i') }}</div>
</div>
</body>
</html>
