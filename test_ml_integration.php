<?php

/**
 * Test script for ML Integration
 * Run this to test if the ML API integration is working
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MLPredictionService;

echo "🧪 Testing ML Integration...\n\n";

// Test 1: Service instantiation
echo "1. Testing service instantiation...\n";
try {
    $mlService = new MLPredictionService();
    echo "✅ Service created successfully\n";
} catch (Exception $e) {
    echo "❌ Service creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Health check
echo "\n2. Testing ML API health check...\n";
try {
    $isHealthy = $mlService->isHealthy();
    if ($isHealthy) {
        echo "✅ ML API is healthy\n";
    } else {
        echo "⚠️  ML API is not responding (make sure Flask API is running on http://127.0.0.1:5000)\n";
    }
} catch (Exception $e) {
    echo "❌ Health check failed: " . $e->getMessage() . "\n";
}

// Test 3: API info
echo "\n3. Testing API info retrieval...\n";
try {
    $info = $mlService->getApiInfo();
    if ($info) {
        echo "✅ API Info retrieved successfully\n";
        echo "   Service: " . ($info['service'] ?? 'Unknown') . "\n";
        echo "   Version: " . ($info['version'] ?? 'Unknown') . "\n";
    } else {
        echo "⚠️  Could not retrieve API info\n";
    }
} catch (Exception $e) {
    echo "❌ API info retrieval failed: " . $e->getMessage() . "\n";
}

// Test 4: Risk prediction
echo "\n4. Testing risk prediction...\n";
try {
    $testData = [
        'avg_score_pct' => 75.0,
        'variation_score_pct' => 15.0,
        'late_submission_pct' => 20.0,
        'missed_submission_pct' => 5.0
    ];
    
    $predictions = $mlService->getRiskPredictions($testData);
    
    if ($predictions['success']) {
        echo "✅ Risk prediction successful\n";
        echo "   Has risks: " . ($predictions['has_risks'] ? 'Yes' : 'No') . "\n";
        echo "   Risk count: " . $predictions['risk_count'] . "\n";
        
        if ($predictions['has_risks']) {
            echo "   Risks detected:\n";
            foreach ($predictions['risks'] as $risk) {
                echo "     - " . $risk['label'] . ": " . $risk['description'] . "\n";
            }
        }
    } else {
        echo "❌ Risk prediction failed: " . ($predictions['error'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Risk prediction failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 ML Integration test completed!\n";
echo "\nTo start the Flask API, run:\n";
echo "cd ML && python flask_api.py\n";
echo "\nThen visit your Laravel app to see the ML predictions in action!\n"; 