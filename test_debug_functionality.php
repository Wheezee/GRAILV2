<?php

/**
 * Test script for ML Debug Functionality
 * This tests the debug API endpoints
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MLPredictionService;

echo "🐛 Testing ML Debug Functionality...\n\n";

// Test 1: Service instantiation
echo "1. Testing service instantiation...\n";
try {
    $mlService = new MLPredictionService();
    echo "✅ Service created successfully\n";
} catch (Exception $e) {
    echo "❌ Service creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Test debug data format
echo "\n2. Testing debug data format...\n";
$testStudentData = [
    'avg_score_pct' => 85.5,
    'variation_score_pct' => 12.3,
    'late_submission_pct' => 15.0,
    'missed_submission_pct' => 2.5
];

echo "Input data:\n";
echo json_encode($testStudentData, JSON_PRETTY_PRINT) . "\n";

// Test 3: Make prediction and show debug info
echo "\n3. Making prediction with debug info...\n";
try {
    $startTime = microtime(true);
    $predictions = $mlService->getRiskPredictions($testStudentData);
    $endTime = microtime(true);
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "✅ Prediction completed in {$responseTime}ms\n";
    echo "Response:\n";
    echo json_encode($predictions, JSON_PRETTY_PRINT) . "\n";
    
    if ($predictions['success']) {
        echo "\n📊 Debug Summary:\n";
        echo "- Has risks: " . ($predictions['has_risks'] ? 'Yes' : 'No') . "\n";
        echo "- Risk count: " . $predictions['risk_count'] . "\n";
        
        if ($predictions['has_risks']) {
            echo "- Risks detected:\n";
            foreach ($predictions['risks'] as $risk) {
                echo "  • " . $risk['label'] . ": " . $risk['description'] . "\n";
            }
        }
    } else {
        echo "❌ Prediction failed: " . ($predictions['error'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Prediction failed: " . $e->getMessage() . "\n";
}

// Test 4: Test different scenarios
echo "\n4. Testing different student scenarios...\n";
$testScenarios = [
    'High Risk Student' => [
        'avg_score_pct' => 45.0,
        'variation_score_pct' => 35.0,
        'late_submission_pct' => 80.0,
        'missed_submission_pct' => 25.0
    ],
    'Average Student' => [
        'avg_score_pct' => 75.0,
        'variation_score_pct' => 15.0,
        'late_submission_pct' => 20.0,
        'missed_submission_pct' => 5.0
    ],
    'Excellent Student' => [
        'avg_score_pct' => 95.0,
        'variation_score_pct' => 5.0,
        'late_submission_pct' => 0.0,
        'missed_submission_pct' => 0.0
    ]
];

foreach ($testScenarios as $scenario => $data) {
    echo "\n--- {$scenario} ---\n";
    echo "Input: " . json_encode($data) . "\n";
    
    try {
        $predictions = $mlService->getRiskPredictions($data);
        echo "Result: " . ($predictions['success'] ? 'Success' : 'Failed') . "\n";
        if ($predictions['success']) {
            echo "Risks: " . $predictions['risk_count'] . "\n";
            if ($predictions['has_risks']) {
                foreach ($predictions['risks'] as $risk) {
                    echo "  - " . $risk['label'] . "\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Debug functionality test completed!\n";
echo "\nTo test the debug UI:\n";
echo "1. Start the Flask API: cd ML && python flask_api.py\n";
echo "2. Visit your grading system\n";
echo "3. Click the 🐛 debug button next to any student's ML risk indicator\n";
echo "4. You'll see the full API request/response in a modal!\n"; 