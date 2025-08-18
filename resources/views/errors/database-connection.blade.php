<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Connection Error</title>
    <style>
        body {
            font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
            margin: 0;
            padding: 20px;
            background-color: #1a1a1a;
            color: #e5e5e5;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .section {
            background: #2a2a2a;
            border: 1px solid #404040;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .section-header {
            background: #333;
            padding: 15px 20px;
            border-bottom: 1px solid #404040;
            font-weight: bold;
            color: #60a5fa;
        }
        .section-content {
            padding: 20px;
        }
        .config-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
            align-items: center;
        }
        .config-label {
            font-weight: bold;
            color: #fbbf24;
        }
        .config-value {
            font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
            background: #1a1a1a;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #404040;
            word-break: break-all;
        }
        .config-value.highlight {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }
        .error-message {
            background: #1a1a1a;
            border: 1px solid #dc2626;
            border-radius: 4px;
            padding: 15px;
            font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .suggestions {
            background: #065f46;
            border: 1px solid #059669;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        .suggestions h4 {
            margin: 0 0 10px 0;
            color: #10b981;
        }
        .suggestions ul {
            margin: 0;
            padding-left: 20px;
        }
        .suggestions li {
            margin-bottom: 5px;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-error {
            background-color: #dc2626;
        }
        .status-warning {
            background-color: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="status-indicator status-error"></span>Database Connection Error</h1>
            <p>Unable to connect to the database. Here's the debugging information:</p>
        </div>

        <div class="section">
            <div class="section-header">
                🔧 Current Database Configuration
            </div>
            <div class="section-content">
                <div class="config-grid">
                    <div class="config-label">Connection Type:</div>
                    <div class="config-value">{{ $dbConfig['connection'] ?? 'Not Set' }}</div>
                    
                    <div class="config-label">Host:</div>
                    <div class="config-value {{ ($dbConfig['host'] ?? '') === '127.0.0.1' || ($dbConfig['host'] ?? '') === 'localhost' ? 'highlight' : '' }}">
                        {{ $dbConfig['host'] ?? 'Not Set' }}
                        @if(($dbConfig['host'] ?? '') === '127.0.0.1' || ($dbConfig['host'] ?? '') === 'localhost')
                            <span style="color: #fbbf24; font-weight: bold;"> ⚠️ Using localhost - check if this is correct for production!</span>
                        @endif
                    </div>
                    
                    <div class="config-label">Port:</div>
                    <div class="config-value">{{ $dbConfig['port'] ?? 'Not Set' }}</div>
                    
                    <div class="config-label">Database:</div>
                    <div class="config-value">{{ $dbConfig['database'] ?? 'Not Set' }}</div>
                    
                    <div class="config-label">Username:</div>
                    <div class="config-value">{{ $dbConfig['username'] ?? 'Not Set' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                🌍 Environment Information
            </div>
            <div class="section-content">
                <div class="config-grid">
                    <div class="config-label">Environment:</div>
                    <div class="config-value">{{ $envInfo['app_env'] ?? 'Not Set' }}</div>
                    
                    <div class="config-label">Debug Mode:</div>
                    <div class="config-value">{{ $envInfo['app_debug'] ? 'Enabled' : 'Disabled' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                ❌ Error Details
            </div>
            <div class="section-content">
                <div class="error-message">{{ $originalMessage }}</div>
                
                <div class="suggestions">
                    <h4>💡 Troubleshooting Suggestions:</h4>
                    <ul>
                        @if(str_contains($originalMessage, 'Connection refused'))
                            <li><strong>Connection Refused:</strong> The database server is not accepting connections. Check if the MySQL service is running.</li>
                            <li>Verify the hostname: <code>{{ $dbConfig['host'] ?? 'Not Set' }}</code> is correct for your environment.</li>
                            <li>For DreamHost: Use <code>mysql.yourdomain.com</code> instead of localhost.</li>
                        @endif
                        
                        @if(str_contains($originalMessage, 'Access denied'))
                            <li><strong>Access Denied:</strong> Check your database username and password.</li>
                            <li>Verify the user <code>{{ $dbConfig['username'] ?? 'Not Set' }}</code> has access to database <code>{{ $dbConfig['database'] ?? 'Not Set' }}</code>.</li>
                        @endif
                        
                        @if(str_contains($originalMessage, 'Unknown database'))
                            <li><strong>Unknown Database:</strong> The database <code>{{ $dbConfig['database'] ?? 'Not Set' }}</code> doesn't exist.</li>
                            <li>Create the database or check the database name in your .env file.</li>
                        @endif
                        
                        <li>Clear Laravel configuration cache: <code>php artisan config:clear</code></li>
                        <li>Check your .env file has the correct database settings.</li>
                        <li>Test direct connection: <code>mysql -h {{ $dbConfig['host'] ?? 'HOST' }} -u {{ $dbConfig['username'] ?? 'USER' }} -p {{ $dbConfig['database'] ?? 'DATABASE' }}</code></li>
                    </ul>
                </div>
            </div>
        </div>

        @if($envInfo['app_env'] === 'local' || $envInfo['app_debug'])
        <div class="section">
            <div class="section-header">
                🔍 Full Exception Details (Debug Mode)
            </div>
            <div class="section-content">
                <div class="error-message">
                    <strong>Exception:</strong> {{ get_class($exception) }}
                    <strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                    
                    <strong>Stack Trace:</strong>
                    {{ $exception->getTraceAsString() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>
