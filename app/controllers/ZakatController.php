<?php

class ZakatController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    public function index() {
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $user = $this->userModel->findById(Session::getUserId());
        
        // Check if user is Muslim (case insensitive)
        if (strtolower($user['religion']) !== 'islam') {
            redirect('index.php?page=profile');
        }
        
        return [
            'user' => $user,
            'pageTitle' => 'Zakat Calculator - UNIFICATION'
        ];
    }
    
    public function calculate($data) {
        if (!Session::isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login to use the calculator'];
        }
        
        $user = $this->userModel->findById(Session::getUserId());
        
        // Check if user is Muslim (case insensitive)
        if (strtolower($user['religion']) !== 'islam') {
            return ['success' => false, 'message' => 'Zakat calculator is only available for Muslim users'];
        }
        
        // Validate input data
        $wealthData = $this->validateWealthData($data);
        if (!$wealthData['valid']) {
            return ['success' => false, 'message' => $wealthData['message']];
        }
        
        // Calculate Zakat
        $calculation = $this->performZakatCalculation($wealthData['data']);
        
        return [
            'success' => true,
            'calculation' => $calculation
        ];
    }
    
    private function validateWealthData($data) {
        $requiredFields = ['cash', 'gold', 'silver', 'investments', 'business', 'other_assets', 'debts', 'expenses'];
        $wealthData = [];
        
        foreach ($requiredFields as $field) {
            $value = isset($data[$field]) ? floatval($data[$field]) : 0;
            if ($value < 0) {
                return ['valid' => false, 'message' => 'Values cannot be negative'];
            }
            $wealthData[$field] = $value;
        }
        
        return ['valid' => true, 'data' => $wealthData];
    }
    
    private function performZakatCalculation($data) {
        $totalWealth = $data['cash'] + $data['gold'] + $data['silver'] + 
                      $data['investments'] + $data['business'] + $data['other_assets'];
        
        $totalLiabilities = $data['debts'] + $data['expenses'];
        $netWealth = $totalWealth - $totalLiabilities;
        
        // Zakat rate is 2.5% (0.025)
        $zakatAmount = $netWealth > 0 ? $netWealth * 0.025 : 0;
        
        // Check if wealth meets Nisab (minimum threshold)
        // Nisab is approximately 87.48g of gold or 612.36g of silver
        // For simplicity, we'll use a monetary equivalent (this should be updated with current gold prices)
        $nisab = 50000; // This should be calculated based on current gold prices
        $meetsNisab = $netWealth >= $nisab;
        
        return [
            'totalWealth' => $totalWealth,
            'totalLiabilities' => $totalLiabilities,
            'netWealth' => $netWealth,
            'zakatAmount' => $zakatAmount,
            'meetsNisab' => $meetsNisab,
            'nisab' => $nisab,
            'zakatRate' => 2.5
        ];
    }
    
    public function getGuidelines() {
        return [
            'nisab' => 'The minimum threshold (Nisab) is equivalent to 87.48g of gold or 612.36g of silver',
            'rate' => 'Zakat rate is 2.5% of eligible wealth',
            'conditions' => [
                'Wealth must be held for one complete lunar year',
                'Wealth must exceed the Nisab threshold',
                'Wealth must be productive or have the potential to grow',
                'Essential expenses and debts are deducted from total wealth'
            ],
            'exemptions' => [
                'Personal residence (if not for investment)',
                'Personal belongings and household items',
                'Business inventory that doesn\'t meet the minimum threshold',
                'Debts that are due immediately'
            ]
        ];
    }
}
