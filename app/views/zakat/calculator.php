<div class="zakat-calculator-page">
    <div class="calculator-header">
        <div class="header-icon">
            <i class="fas fa-mosque"></i>
        </div>
        <div class="header-content">
            <h1>Zakat Calculator</h1>
            <p class="header-subtitle">Calculate your Zakat obligation (2.5% of eligible wealth)</p>
        </div>
    </div>

    <div class="calculator-container">
        <div class="calculator-form-section">
            <form id="zakatForm" class="zakat-form">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-coins"></i>
                        Wealth Categories
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cash">
                                <i class="fas fa-money-bill-wave"></i>
                                Cash & Bank Accounts
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="cash" name="cash" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="gold">
                                <i class="fas fa-gem"></i>
                                Gold Value
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="gold" name="gold" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="silver">
                                <i class="fas fa-medal"></i>
                                Silver Value
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="silver" name="silver" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="investments">
                                <i class="fas fa-chart-line"></i>
                                Investments & Stocks
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="investments" name="investments" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="business">
                                <i class="fas fa-store"></i>
                                Business Assets
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="business" name="business" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="other_assets">
                                <i class="fas fa-boxes"></i>
                                Other Assets
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="other_assets" name="other_assets" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Liabilities & Debts
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="debts">
                                <i class="fas fa-exclamation-triangle"></i>
                                Outstanding Debts
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="debts" name="debts" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="expenses">
                                <i class="fas fa-shopping-cart"></i>
                                Essential Expenses (Next 3 months)
                            </label>
                            <div class="input-group">
                                <span class="currency">৳</span>
                                <input type="number" id="expenses" name="expenses" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-calculate">
                        <i class="fas fa-calculator"></i>
                        Calculate Zakat
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="clearForm()">
                        <i class="fas fa-eraser"></i>
                        Clear Form
                    </button>
                </div>
            </form>
        </div>

        <div class="calculator-right-section">
            <div class="calculator-result-section" id="resultSection">
                <div class="result-header">
                    <h3>
                        <i class="fas fa-chart-pie"></i>
                        Zakat Calculation Result
                    </h3>
                </div>
                
                <div class="result-summary">
                    <div class="result-card">
                        <div class="result-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="result-content">
                            <span class="result-label">Total Wealth</span>
                            <span class="result-value" id="totalWealth">৳0.00</span>
                        </div>
                    </div>
                    
                    <div class="result-card">
                        <div class="result-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="result-content">
                            <span class="result-label">Total Liabilities</span>
                            <span class="result-value" id="totalLiabilities">৳0.00</span>
                        </div>
                    </div>
                    
                    <div class="result-card">
                        <div class="result-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="result-content">
                            <span class="result-label">Net Wealth</span>
                            <span class="result-value" id="netWealth">৳0.00</span>
                        </div>
                    </div>
                    
                    <div class="result-card highlight">
                        <div class="result-icon">
                            <i class="fas fa-mosque"></i>
                        </div>
                        <div class="result-content">
                            <span class="result-label">Zakat Amount (2.5%)</span>
                            <span class="result-value" id="zakatAmount">৳0.00</span>
                        </div>
                    </div>
                </div>

                <div class="nisab-status" id="nisabStatus">
                    <div class="status-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="status-content">
                        <h4>Nisab Status</h4>
                        <p id="nisabMessage">Enter your wealth details above to see if you meet the Nisab threshold...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="zakat-guidelines-section">
        <div class="guidelines-container">
            <div class="guidelines-header">
                <h2>
                    <i class="fas fa-book"></i>
                    Zakat Guidelines & Rules
                </h2>
                <p class="guidelines-subtitle">Essential information about Zakat obligations and calculations</p>
            </div>
            
            <div class="guidelines-content">
                <div class="guideline-item">
                    <div class="guideline-number">1</div>
                    <div class="guideline-text">
                        <h3>Holding Period (Hawl)</h3>
                        <p>Wealth must be held for one complete lunar year (Hijri calendar) before Zakat becomes obligatory. The year begins from the date you first possessed wealth equal to or exceeding the Nisab threshold.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">2</div>
                    <div class="guideline-text">
                        <h3>Nisab Threshold</h3>
                        <p>The minimum threshold (Nisab) is equivalent to 87.48 grams of gold or 612.36 grams of silver. If your net wealth falls below this threshold, Zakat is not obligatory. The Nisab value should be calculated based on current market prices.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">3</div>
                    <div class="guideline-text">
                        <h3>Zakat Rate</h3>
                        <p>Zakat rate is 2.5% (1/40) of eligible wealth above the Nisab threshold. This rate is fixed and applies to all types of wealth that are subject to Zakat.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">4</div>
                    <div class="guideline-text">
                        <h3>Eligible Wealth Categories</h3>
                        <p>Zakat is obligatory on: cash and bank deposits, gold and silver, business assets, investments, stocks, rental income, and other liquid assets. All wealth must be productive or have the potential to grow.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">5</div>
                    <div class="guideline-text">
                        <h3>Exemptions from Zakat</h3>
                        <p>Personal residence (if not for investment), household items, personal belongings, business inventory below Nisab, immediate debts, and essential living expenses are exempt from Zakat.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">6</div>
                    <div class="guideline-text">
                        <h3>Deductions from Wealth</h3>
                        <p>Outstanding debts that are due immediately, essential living expenses for the next year, and business expenses can be deducted from total wealth before calculating Zakat.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">7</div>
                    <div class="guideline-text">
                        <h3>Payment Timing</h3>
                        <p>Zakat should be paid annually on the same date each year (based on Hijri calendar). It can be paid in advance or in installments, but the full amount should be paid within the Zakat year.</p>
                    </div>
                </div>
                
                <div class="guideline-item">
                    <div class="guideline-number">8</div>
                    <div class="guideline-text">
                        <h3>Recipients of Zakat</h3>
                        <p>Zakat can only be given to: the poor, the needy, Zakat collectors, those whose hearts are to be reconciled, freeing slaves, those in debt, in the cause of Allah, and wayfarers (Quran 9:60).</p>
                    </div>
                </div>
            </div>
            
            <div class="guidelines-note">
                <div class="note-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="note-content">
                    <h4>Important Disclaimer</h4>
                    <p>This calculator provides an estimate based on general Zakat principles. Zakat calculations can be complex and may vary based on individual circumstances, local interpretations, and specific situations. Please consult with a qualified Islamic scholar or your local mosque for personalized guidance on your Zakat obligations.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('zakatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    calculateZakat();
});

function calculateZakat() {
    const formData = new FormData(document.getElementById('zakatForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Convert to numbers
    const cash = parseFloat(data.cash) || 0;
    const gold = parseFloat(data.gold) || 0;
    const silver = parseFloat(data.silver) || 0;
    const investments = parseFloat(data.investments) || 0;
    const business = parseFloat(data.business) || 0;
    const otherAssets = parseFloat(data.other_assets) || 0;
    const debts = parseFloat(data.debts) || 0;
    const expenses = parseFloat(data.expenses) || 0;
    
    const totalWealth = cash + gold + silver + investments + business + otherAssets;
    const totalLiabilities = debts + expenses;
    const netWealth = totalWealth - totalLiabilities;
    const zakatAmount = netWealth > 0 ? netWealth * 0.025 : 0;
    
    // Nisab threshold (this should be updated with current gold prices)
    const nisab = 50000; // This should be calculated based on current gold prices
    const meetsNisab = netWealth >= nisab;
    
    // Update result display
    document.getElementById('totalWealth').textContent = '৳' + totalWealth.toLocaleString('en-BD', {minimumFractionDigits: 2});
    document.getElementById('totalLiabilities').textContent = '৳' + totalLiabilities.toLocaleString('en-BD', {minimumFractionDigits: 2});
    document.getElementById('netWealth').textContent = '৳' + netWealth.toLocaleString('en-BD', {minimumFractionDigits: 2});
    document.getElementById('zakatAmount').textContent = '৳' + zakatAmount.toLocaleString('en-BD', {minimumFractionDigits: 2});
    
    // Update Nisab status
    const nisabMessage = document.getElementById('nisabMessage');
    if (meetsNisab) {
        nisabMessage.textContent = `Your wealth (৳${netWealth.toLocaleString('en-BD', {minimumFractionDigits: 2})}) exceeds the Nisab threshold (৳${nisab.toLocaleString('en-BD')}). Zakat is obligatory.`;
        document.getElementById('nisabStatus').className = 'nisab-status meets-nisab';
    } else {
        nisabMessage.textContent = `Your wealth (৳${netWealth.toLocaleString('en-BD', {minimumFractionDigits: 2})}) is below the Nisab threshold (৳${nisab.toLocaleString('en-BD')}). Zakat is not obligatory.`;
        document.getElementById('nisabStatus').className = 'nisab-status below-nisab';
    }
    
    // Scroll to result section for better UX
    document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth' });
}

function clearForm() {
    document.getElementById('zakatForm').reset();
    
    // Reset all result values to default
    document.getElementById('totalWealth').textContent = '৳0.00';
    document.getElementById('totalLiabilities').textContent = '৳0.00';
    document.getElementById('netWealth').textContent = '৳0.00';
    document.getElementById('zakatAmount').textContent = '৳0.00';
    
    // Reset Nisab status
    document.getElementById('nisabMessage').textContent = 'Enter your wealth details above to see if you meet the Nisab threshold...';
    document.getElementById('nisabStatus').className = 'nisab-status';
}
</script>
