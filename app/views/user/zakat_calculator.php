<div class="zakat-calculator">
    <h3>🕌 Zakat Calculator</h3>
    <p class="zakat-intro">Calculate your Zakat obligation (2.5% of eligible wealth)</p>
    
    <form id="zakatForm" class="zakat-form">
        <div class="wealth-categories">
            <h4>Wealth Categories</h4>
            
            <div class="form-group">
                <label for="cash">Cash & Bank Accounts (৳)</label>
                <input type="number" id="cash" name="cash" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="gold">Gold Value (৳)</label>
                <input type="number" id="gold" name="gold" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="silver">Silver Value (৳)</label>
                <input type="number" id="silver" name="silver" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="investments">Investments & Stocks (৳)</label>
                <input type="number" id="investments" name="investments" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="business">Business Assets (৳)</label>
                <input type="number" id="business" name="business" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="other_assets">Other Assets (৳)</label>
                <input type="number" id="other_assets" name="other_assets" min="0" step="0.01" value="0">
            </div>
        </div>
        
        <div class="liabilities">
            <h4>Liabilities & Debts</h4>
            
            <div class="form-group">
                <label for="debts">Outstanding Debts (৳)</label>
                <input type="number" id="debts" name="debts" min="0" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="expenses">Essential Expenses (Next 3 months) (৳)</label>
                <input type="number" id="expenses" name="expenses" min="0" step="0.01" value="0">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Calculate Zakat</button>
    </form>
    
    <div id="zakatResult" class="zakat-result" style="display: none;">
        <h4>Zakat Calculation Result</h4>
        <div class="result-summary">
            <div class="result-item">
                <span class="label">Total Wealth:</span>
                <span class="value" id="totalWealth">৳0.00</span>
            </div>
            <div class="result-item">
                <span class="label">Total Liabilities:</span>
                <span class="value" id="totalLiabilities">৳0.00</span>
            </div>
            <div class="result-item">
                <span class="label">Net Wealth:</span>
                <span class="value" id="netWealth">৳0.00</span>
            </div>
            <div class="result-item highlight">
                <span class="label">Zakat Amount (2.5%):</span>
                <span class="value" id="zakatAmount">৳0.00</span>
            </div>
        </div>
        
        <div class="zakat-info">
            <h5>📋 Zakat Guidelines:</h5>
            <ul>
                <li>Zakat is obligatory on wealth that has been held for one lunar year</li>
                <li>The minimum threshold (Nisab) is equivalent to 87.48g of gold or 612.36g of silver</li>
                <li>Zakat rate is 2.5% of eligible wealth</li>
                <li>Essential expenses and debts are deducted from total wealth</li>
                <li>Consult with a qualified Islamic scholar for specific guidance</li>
            </ul>
        </div>
    </div>
</div>

<style>
.zakat-calculator {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-top: 2rem;
}

.zakat-intro {
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.wealth-categories,
.liabilities {
    margin-bottom: 2rem;
}

.wealth-categories h4,
.liabilities h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.zakat-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.zakat-form .form-group {
    margin-bottom: 1rem;
}

.zakat-result {
    margin-top: 2rem;
    padding: 1.5rem;
    background: rgba(0, 166, 81, 0.1);
    border: 1px solid var(--primary-color);
    border-radius: var(--border-radius);
}

.result-summary {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.result-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--card-bg);
    border-radius: var(--border-radius);
}

.result-item.highlight {
    background: var(--primary-color);
    color: white;
    font-weight: bold;
}

.zakat-info {
    background: rgba(255, 193, 7, 0.1);
    padding: 1rem;
    border-radius: var(--border-radius);
    border: 1px solid #ffc107;
}

.zakat-info h5 {
    color: #856404;
    margin-bottom: 0.5rem;
}

.zakat-info ul {
    margin: 0;
    padding-left: 1.5rem;
}

.zakat-info li {
    margin-bottom: 0.5rem;
    color: #856404;
}

@media (max-width: 768px) {
    .zakat-form {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<script>
document.getElementById('zakatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    calculateZakat();
});

function calculateZakat() {
    const cash = parseFloat(document.getElementById('cash').value) || 0;
    const gold = parseFloat(document.getElementById('gold').value) || 0;
    const silver = parseFloat(document.getElementById('silver').value) || 0;
    const investments = parseFloat(document.getElementById('investments').value) || 0;
    const business = parseFloat(document.getElementById('business').value) || 0;
    const otherAssets = parseFloat(document.getElementById('other_assets').value) || 0;
    const debts = parseFloat(document.getElementById('debts').value) || 0;
    const expenses = parseFloat(document.getElementById('expenses').value) || 0;
    
    const totalWealth = cash + gold + silver + investments + business + otherAssets;
    const totalLiabilities = debts + expenses;
    const netWealth = totalWealth - totalLiabilities;
    const zakatAmount = netWealth > 0 ? netWealth * 0.025 : 0;
    
    document.getElementById('totalWealth').textContent = '৳' + totalWealth.toFixed(2);
    document.getElementById('totalLiabilities').textContent = '৳' + totalLiabilities.toFixed(2);
    document.getElementById('netWealth').textContent = '৳' + netWealth.toFixed(2);
    document.getElementById('zakatAmount').textContent = '৳' + zakatAmount.toFixed(2);
    
    document.getElementById('zakatResult').style.display = 'block';
    
    document.getElementById('zakatResult').scrollIntoView({ behavior: 'smooth' });
}
</script> 