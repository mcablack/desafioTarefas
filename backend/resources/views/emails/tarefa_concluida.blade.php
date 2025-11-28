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
            background: #10b981;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info {
            margin: 20px 0;
            padding: 15px;
            background: #d1fae5;
            border-radius: 6px;
            border-left: 4px solid #10b981;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon"></div>
            <h1>Tarefa Concluída!</h1>
        </div>
        
        <p>Parabéns! A seguinte tarefa foi marcada como concluída:</p>
        
        <div class="info">
            <h3>{{ $tarefa->titulo }}</h3>
            <p><strong>Descrição:</strong> {{ $tarefa->descricao ?? 'Sem descrição' }}</p>
            <p><strong>Prioridade:</strong> {{ ucfirst($tarefa->prioridade) }}</p>
            <p><strong>Concluída em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        
        <p style="color: #059669; font-weight: bold;">
            ✨ Excelente trabalho!
        </p>
        
        <p style="color: #6b7280; font-size: 13px; margin-top: 20px;">
            Continue assim e mantenha a produtividade alta!
        </p>
    </div>
</body>
</html>