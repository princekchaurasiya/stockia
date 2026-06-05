import { createRoot } from 'react-dom/client';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

function StockChart() {
    const rootEl = document.getElementById('stock-chart-root');
    if (!rootEl) return;
    const payload = rootEl.dataset.payload;
    const data = payload ? JSON.parse(payload) : [];
    const column = rootEl.dataset.column || 'value';

    const chart = (
        <ResponsiveContainer width="100%" height={300}>
            <LineChart data={data} margin={{ top: 5, right: 20, left: 0, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="label" />
                <YAxis />
                <Tooltip />
                <Line type="monotone" dataKey="value" stroke="#0d6efd" strokeWidth={2} dot={false} />
            </LineChart>
        </ResponsiveContainer>
    );

    const root = createRoot(rootEl);
    root.render(chart);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', StockChart);
} else {
    StockChart();
}

document.addEventListener('livewire:navigated', StockChart);
document.addEventListener('chart-update', () => setTimeout(StockChart, 100));
window.renderStockChart = StockChart;
