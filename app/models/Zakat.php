<?php

class Zakat {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function saveCalculation($userId, $calculationData) {
        $stmt = $this->db->prepare("
            INSERT INTO zakat_calculations (
                user_id, total_wealth, total_liabilities, net_wealth, 
                zakat_amount, meets_nisab, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $userId,
            $calculationData['totalWealth'],
            $calculationData['totalLiabilities'],
            $calculationData['netWealth'],
            $calculationData['zakatAmount'],
            $calculationData['meetsNisab'] ? 1 : 0
        ]);
    }
    
    public function getCalculationsByUser($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * FROM zakat_calculations 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getLatestCalculation($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM zakat_calculations 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    public function getCurrentGoldPrice() {
        // This would typically fetch from an API
        // For now, return a static value (should be updated regularly)
        return 5000; // Price per gram in BDT
    }
    
    public function getCurrentSilverPrice() {
        // This would typically fetch from an API
        // For now, return a static value (should be updated regularly)
        return 80; // Price per gram in BDT
    }
    
    public function calculateNisab() {
        $goldPrice = $this->getCurrentGoldPrice();
        $silverPrice = $this->getCurrentSilverPrice();
        
        // Nisab in grams
        $goldNisab = 87.48; // grams
        $silverNisab = 612.36; // grams
        
        return [
            'gold' => $goldNisab * $goldPrice,
            'silver' => $silverNisab * $silverPrice,
            'gold_grams' => $goldNisab,
            'silver_grams' => $silverNisab
        ];
    }
}
