# 📈 Indian Stock Market Screener

A professional, real-time stock screener for Indian markets with data from Yahoo Finance API.

![Stock Screener](https://img.shields.io/badge/Status-Live-success)
![License](https://img.shields.io/badge/License-MIT-blue)

## ✨ Features

- **Real-Time Data**: Live stock prices from Yahoo Finance API
- **Complete Coverage**: Nifty 50 stocks with proper NSE sector classification
- **Advanced Filtering**: 10+ filter types including sector, market cap, P/E, P/B, ROE, dividend yield
- **Cascading Filters**: Sub-sectors update based on selected sector
- **Multiple Views**: Table view and interactive charts
- **Dark Mode**: Toggle between light and dark themes
- **Auto-Refresh**: Prices update automatically every 5 minutes
- **Performance Indicators**: Good/Neutral/Bad status for each stock
- **CSV Export**: Download filtered results
- **Responsive Design**: Works on desktop and mobile

## 🚀 Live Demo

Visit: `https://YOUR_USERNAME.github.io/indian-stock-screener/`

## 📊 Stock Data

- **Nifty 50**: All 50 constituents with real-time prices
- **Proper Categorization**: NSE 4-tier classification (Sector → Industry → Basic Industry)
- **Fundamentals**: P/E, P/B, ROE, Dividend Yield
- **Market Data**: Price, Change %, Market Cap, 52-week High/Low

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **API**: Yahoo Finance (free, no API key required)
- **Hosting**: GitHub Pages
- **Data Source**: NSE India official stock universe

## 📁 Project Structure

```
├── index.html                      # Main application
├── screener_styles.css             # Styling with dark mode
├── screener_script_realtime.js     # Application logic
├── stock_universe.js               # Official Nifty 50 stocks
├── yahoo_finance_api.js            # Yahoo Finance integration
└── README.md                       # This file
```

## 🔧 Local Development

To run locally with real-time data:

```bash
# Clone the repository
git clone https://github.com/YOUR_USERNAME/indian-stock-screener.git
cd indian-stock-screener

# Start local server (required for API calls)
python3 -m http.server 8000

# Open in browser
open http://localhost:8000
```

**Note**: Opening `index.html` directly won't work due to CORS restrictions. You must use a local server.

## 📖 Usage

1. **Select Index**: Choose from Nifty 50, Bank, IT, Pharma, etc.
2. **Apply Filters**: Use sidebar filters to narrow down stocks
3. **Sort Results**: Sort by price, change %, P/E, score, etc.
4. **View Charts**: Switch to charts view for visual analysis
5. **Export Data**: Download filtered results as CSV

## 🎨 Features in Detail

### Filtering Options

- **Index**: Nifty 50, Bank, IT, Pharma, Auto, FMCG, Metal
- **Sector**: Financial Services, IT, Healthcare, etc.
- **Sub-Sector**: Cascading filter based on selected sector
- **Market Cap**: Large/Mid/Small cap with custom ranges
- **P/E Ratio**: Low (<15), Moderate (15-25), High (>25)
- **P/B Ratio**: Custom range filtering
- **Dividend Yield**: High (>2%), Moderate (1-2%)
- **ROE**: Return on Equity percentage
- **Price Change**: Top gainers/losers
- **Performance**: Good/Neutral/Bad stocks

### Sector Classification

Uses official NSE 4-tier classification:
1. **Macro-Economic Sector**
2. **Sector** (e.g., Financial Services)
3. **Industry** (e.g., Banks)
4. **Basic Industry** (e.g., Private Sector Bank)

## 🔄 Auto-Refresh

Stock prices automatically refresh every 5 minutes to keep data current.

## 🌙 Dark Mode

Toggle between light and dark themes. Preference is saved in browser.

## 📱 Responsive Design

Optimized for:
- Desktop (1920x1080+)
- Laptop (1366x768+)
- Tablet (768x1024)
- Mobile (375x667+)

## 🐛 Known Issues

- **CORS**: Must be served via HTTP/HTTPS (not file://)
- **Rate Limiting**: Yahoo Finance may limit requests if too frequent
- **Data Accuracy**: Prices are indicative, verify before trading

## 🤝 Contributing

Contributions welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📝 License

MIT License - feel free to use for personal or commercial projects.

## ⚠️ Disclaimer

This tool is for informational purposes only. Stock prices are indicative and may not reflect actual market prices. Always verify data before making investment decisions. The developer is not responsible for any trading losses.

## 🙏 Acknowledgments

- **Data Source**: Yahoo Finance API
- **Stock Universe**: NSE India official constituents
- **Sector Classification**: NSE Indices Ltd.

## 📧 Contact

For issues or suggestions, please open an issue on GitHub.

---

**Made with ❤️ for Indian stock market enthusiasts**
