
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #1a73e8;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-pendente { background: #fef3c7; color: #92400e; }
        .badge-media { background: #fef3c7; color: #92400e; }
        .info { margin: 20px 0; padding: 15px; background: #f9fafb; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Nova Tarefa Criada!</h1>
        </div>
        
        <p>Olá! Uma nova tarefa foi criada no sistema:</p>
        
        <div class="info">
            <h3>{{ $tarefa->titulo }}</h3>
            <p><strong>Descrição:</strong> {{ $tarefa->descricao ?? 'Sem descrição' }}</p>
            
            <p>
                <strong>Status:</strong> 
                <span class="badge badge-{{ $tarefa->status }}">
                    {{ ucfirst(str_replace('_', ' ', $tarefa->status)) }}
                </span>
            </p>
            
            <p>
                <strong>Prioridade:</strong> 
                <span class="badge badge-{{ $tarefa->prioridade }}">
                    {{ ucfirst($tarefa->prioridade) }}
                </span>
            </p>
            
            @if($tarefa->data_limite)
            <p><strong>Data Limite:</strong> {{ $tarefa->data_limite->format('d/m/Y') }}</p>
            @endif
        </div>
        
        <p style="color: #6b7280; font-size: 13px; margin-top: 20px;">
            Acesse o sistema para mais detalhes.
        </p>
    </div>
</body>
</html>


