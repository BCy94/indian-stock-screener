// Complete Stock Universe for Indian Markets
// Official NSE constituents with proper sector categorization
// Updated: January 2026

const STOCK_UNIVERSE = {
    // Nifty 50 - Official constituents as of Jan 2026
    NIFTY50: [
        { symbol: 'ADANIENT', name: 'Adani Enterprises Ltd', sector: 'Diversified', industry: 'Conglomerate', basicIndustry: 'Diversified Business' },
        { symbol: 'ADANIPORTS', name: 'Adani Ports and Special Economic Zone Ltd', sector: 'Services', industry: 'Transport Infrastructure', basicIndustry: 'Port & Port Services' },
        { symbol: 'APOLLOHOSP', name: 'Apollo Hospitals Enterprise Ltd', sector: 'Healthcare', industry: 'Healthcare Services', basicIndustry: 'Hospital' },
        { symbol: 'ASIANPAINT', name: 'Asian Paints Ltd', sector: 'Consumer Durables', industry: 'Consumer Durables', basicIndustry: 'Paints' },
        { symbol: 'AXISBANK', name: 'Axis Bank Ltd', sector: 'Financial Services', industry: 'Banks', basicIndustry: 'Private Sector Bank' },
        { symbol: 'BAJAJ-AUTO', name: 'Bajaj Auto Ltd', sector: 'Automobile and Auto Components', industry: 'Automobiles', basicIndustry: 'Two/Three Wheelers' },
        { symbol: 'BAJAJFINSV', name: 'Bajaj Finserv Ltd', sector: 'Financial Services', industry: 'Finance', basicIndustry: 'Holding Company' },
        { symbol: 'BAJFINANCE', name: 'Bajaj Finance Ltd', sector: 'Financial Services', industry: 'Finance', basicIndustry: 'Non Banking Financial Company (NBFC)' },
        { symbol: 'BEL', name: 'Bharat Electronics Ltd', sector: 'Capital Goods', industry: 'Industrial Manufacturing', basicIndustry: 'Aerospace & Defense' },
        { symbol: 'BHARTIARTL', name: 'Bharti Airtel Ltd', sector: 'Telecom', industry: 'Telecom Services', basicIndustry: 'Telecom - Services' },
        { symbol: 'CIPLA', name: 'Cipla Ltd', sector: 'Healthcare', industry: 'Pharmaceuticals', basicIndustry: 'Pharmaceuticals' },
        { symbol: 'COALINDIA', name: 'Coal India Ltd', sector: 'Oil Gas & Consumable Fuels', industry: 'Consumable Fuels', basicIndustry: 'Coal' },
        { symbol: 'DRREDDY', name: 'Dr. Reddy\'s Laboratories Ltd', sector: 'Healthcare', industry: 'Pharmaceuticals', basicIndustry: 'Pharmaceuticals' },
        { symbol: 'EICHERMOT', name: 'Eicher Motors Ltd', sector: 'Automobile and Auto Components', industry: 'Automobiles', basicIndustry: 'Two/Three Wheelers' },
        { symbol: 'GRASIM', name: 'Grasim Industries Ltd', sector: 'Cement & Cement Products', industry: 'Cement & Cement Products', basicIndustry: 'Cement - Products' },
        { symbol: 'HCLTECH', name: 'HCL Technologies Ltd', sector: 'Information Technology', industry: 'IT - Software', basicIndustry: 'Computers - Software & Consulting' },
        { symbol: 'HDFCBANK', name: 'HDFC Bank Ltd', sector: 'Financial Services', industry: 'Banks', basicIndustry: 'Private Sector Bank' },
        { symbol: 'HDFCLIFE', name: 'HDFC Life Insurance Company Ltd', sector: 'Financial Services', industry: 'Insurance', basicIndustry: 'Life Insurance' },
        { symbol: 'HINDALCO', name: 'Hindalco Industries Ltd', sector: 'Metals & Mining', industry: 'Non - Ferrous Metals', basicIndustry: 'Aluminium' },
        { symbol: 'HINDUNILVR', name: 'Hindustan Unilever Ltd', sector: 'Fast Moving Consumer Goods', industry: 'Household Products', basicIndustry: 'Personal Care' },
        { symbol: 'ICICIBANK', name: 'ICICI Bank Ltd', sector: 'Financial Services', industry: 'Banks', basicIndustry: 'Private Sector Bank' },
        { symbol: 'INDIGO', name: 'InterGlobe Aviation Ltd', sector: 'Services', industry: 'Transport Services', basicIndustry: 'Airline' },
        { symbol: 'INFY', name: 'Infosys Ltd', sector: 'Information Technology', industry: 'IT - Software', basicIndustry: 'Computers - Software & Consulting' },
        { symbol: 'ITC', name: 'ITC Ltd', sector: 'Fast Moving Consumer Goods', industry: 'Diversified FMCG', basicIndustry: 'Diversified' },
        { symbol: 'JIOFIN', name: 'Jio Financial Services Ltd', sector: 'Financial Services', industry: 'Finance', basicIndustry: 'Non Banking Financial Company (NBFC)' },
        { symbol: 'JSWSTEEL', name: 'JSW Steel Ltd', sector: 'Metals & Mining', industry: 'Ferrous Metals', basicIndustry: 'Iron & Steel' },
        { symbol: 'KOTAKBANK', name: 'Kotak Mahindra Bank Ltd', sector: 'Financial Services', industry: 'Banks', basicIndustry: 'Private Sector Bank' },
        { symbol: 'LT', name: 'Larsen & Toubro Ltd', sector: 'Capital Goods', industry: 'Construction', basicIndustry: 'Civil Construction' },
        { symbol: 'M&M', name: 'Mahindra & Mahindra Ltd', sector: 'Automobile and Auto Components', industry: 'Automobiles', basicIndustry: 'Passenger Cars & Utility Vehicles' },
        { symbol: 'MARUTI', name: 'Maruti Suzuki India Ltd', sector: 'Automobile and Auto Components', industry: 'Automobiles', basicIndustry: 'Passenger Cars & Utility Vehicles' },
        { symbol: 'MAXHEALTH', name: 'Max Healthcare Institute Ltd', sector: 'Healthcare', industry: 'Healthcare Services', basicIndustry: 'Hospital' },
        { symbol: 'NESTLEIND', name: 'Nestle India Ltd', sector: 'Fast Moving Consumer Goods', industry: 'Food Products', basicIndustry: 'Packaged Foods' },
        { symbol: 'NTPC', name: 'NTPC Ltd', sector: 'Power', industry: 'Power Generation', basicIndustry: 'Power Generation' },
        { symbol: 'ONGC', name: 'Oil & Natural Gas Corporation Ltd', sector: 'Oil Gas & Consumable Fuels', industry: 'Oil', basicIndustry: 'Oil Exploration / Production' },
        { symbol: 'POWERGRID', name: 'Power Grid Corporation of India Ltd', sector: 'Power', industry: 'Power Distribution', basicIndustry: 'Power - Transmission' },
        { symbol: 'RELIANCE', name: 'Reliance Industries Ltd', sector: 'Oil Gas & Consumable Fuels', industry: 'Refineries & Marketing', basicIndustry: 'Refineries' },
        { symbol: 'SBILIFE', name: 'SBI Life Insurance Company Ltd', sector: 'Financial Services', industry: 'Insurance', basicIndustry: 'Life Insurance' },
        { symbol: 'SBIN', name: 'State Bank of India', sector: 'Financial Services', industry: 'Banks', basicIndustry: 'Public Sector Bank' },
        { symbol: 'SHRIRAMFIN', name: 'Shriram Finance Ltd', sector: 'Financial Services', industry: 'Finance', basicIndustry: 'Non Banking Financial Company (NBFC)' },
        { symbol: 'SUNPHARMA', name: 'Sun Pharmaceutical Industries Ltd', sector: 'Healthcare', industry: 'Pharmaceuticals', basicIndustry: 'Pharmaceuticals' },
        { symbol: 'TATACONSUM', name: 'Tata Consumer Products Ltd', sector: 'Fast Moving Consumer Goods', industry: 'Food Products', basicIndustry: 'Tea & Coffee' },
        { symbol: 'TATAMOTORS', name: 'Tata Motors Ltd', sector: 'Automobile and Auto Components', industry: 'Automobiles', basicIndustry: 'Passenger Cars & Utility Vehicles' },
        { symbol: 'TATASTEEL', name: 'Tata Steel Ltd', sector: 'Metals & Mining', industry: 'Ferrous Metals', basicIndustry: 'Iron & Steel' },
        { symbol: 'TCS', name: 'Tata Consultancy Services Ltd', sector: 'Information Technology', industry: 'IT - Software', basicIndustry: 'Computers - Software & Consulting' },
        { symbol: 'TECHM', name: 'Tech Mahindra Ltd', sector: 'Information Technology', industry: 'IT - Software', basicIndustry: 'Computers - Software & Consulting' },
        { symbol: 'TITAN', name: 'Titan Company Ltd', sector: 'Consumer Durables', industry: 'Consumer Durables', basicIndustry: 'Gems Jewellery And Watches' },
        { symbol: 'TRENT', name: 'Trent Ltd', sector: 'Consumer Services', industry: 'Retailing', basicIndustry: 'Retailing' },
        { symbol: 'ULTRACEMCO', name: 'UltraTech Cement Ltd', sector: 'Cement & Cement Products', industry: 'Cement & Cement Products', basicIndustry: 'Cement' },
        { symbol: 'WIPRO', name: 'Wipro Ltd', sector: 'Information Technology', industry: 'IT - Software', basicIndustry: 'Computers - Software & Consulting' }
    ],

    // Additional stocks for other indices (will be populated via API)
    NIFTYBANK: ['HDFCBANK', 'ICICIBANK', 'SBIN', 'KOTAKBANK', 'AXISBANK', 'INDUSINDBK', 'BANDHANBNK', 'FEDERALBNK', 'IDFCFIRSTB', 'PNB', 'BANKBARODA', 'AUBANK'],
    NIFTYIT: ['TCS', 'INFY', 'HCLTECH', 'WIPRO', 'TECHM', 'LTIM', 'PERSISTENT', 'COFORGE', 'MPHASIS', 'LTTS'],
    NIFTYPHARMA: ['SUNPHARMA', 'DRREDDY', 'CIPLA', 'DIVISLAB', 'LUPIN', 'TORNTPHARM', 'BIOCON', 'ALKEM', 'AUROPHARMA', 'GLENMARK'],
    NIFTYAUTO: ['MARUTI', 'M&M', 'TATAMOTORS', 'BAJAJ-AUTO', 'EICHERMOT', 'HEROMOTOCO', 'ASHOKLEY', 'TVSMOTOR', 'BOSCHLTD', 'MOTHERSON', 'BALKRISIND', 'MRF', 'APOLLOTYRE', 'EXIDEIND', 'AMARAJABAT'],
    NIFTYFMCG: ['HINDUNILVR', 'ITC', 'NESTLEIND', 'BRITANNIA', 'TATACONSUM', 'DABUR', 'GODREJCP', 'MARICO', 'COLPAL', 'PGHH', 'VBL', 'MCDOWELL-N', 'EMAMILTD', 'RADICO', 'TATACHEMICALS'],
    NIFTYMETAL: ['TATASTEEL', 'JSWSTEEL', 'HINDALCO', 'VEDL', 'JINDALSTEL', 'NMDC', 'NATIONALUM', 'SAIL', 'HINDZINC', 'COALINDIA', 'MOIL', 'WELCORP', 'RATNAMANI', 'JSWENERGY', 'ADANIENT']
};

// Sector mapping for proper categorization
const SECTOR_MAPPING = {
    'Financial Services': {
        industries: ['Banks', 'Finance', 'Insurance'],
        basicIndustries: {
            'Banks': ['Private Sector Bank', 'Public Sector Bank'],
            'Finance': ['Non Banking Financial Company (NBFC)', 'Holding Company', 'Investment Company'],
            'Insurance': ['Life Insurance', 'General Insurance', 'Health Insurance']
        }
    },
    'Information Technology': {
        industries: ['IT - Software', 'IT - Hardware'],
        basicIndustries: {
            'IT - Software': ['Computers - Software & Consulting', 'Internet & Catalogue Retail'],
            'IT - Hardware': ['Computers - Hardware']
        }
    },
    'Healthcare': {
        industries: ['Pharmaceuticals', 'Healthcare Services'],
        basicIndustries: {
            'Pharmaceuticals': ['Pharmaceuticals', 'Biotechnology'],
            'Healthcare Services': ['Hospital', 'Diagnostics']
        }
    },
    'Automobile and Auto Components': {
        industries: ['Automobiles', 'Auto Components'],
        basicIndustries: {
            'Automobiles': ['Passenger Cars & Utility Vehicles', 'Commercial Vehicles', 'Two/Three Wheelers'],
            'Auto Components': ['Auto Components & Equipments']
        }
    },
    'Fast Moving Consumer Goods': {
        industries: ['Food Products', 'Household Products', 'Personal Products', 'Diversified FMCG'],
        basicIndustries: {
            'Food Products': ['Packaged Foods', 'Tea & Coffee', 'Edible Oil', 'Sugar'],
            'Household Products': ['Household Products'],
            'Personal Products': ['Personal Care'],
            'Diversified FMCG': ['Diversified']
        }
    },
    'Oil Gas & Consumable Fuels': {
        industries: ['Oil', 'Refineries & Marketing', 'Consumable Fuels'],
        basicIndustries: {
            'Oil': ['Oil Exploration / Production', 'Oil Marketing & Distribution'],
            'Refineries & Marketing': ['Refineries'],
            'Consumable Fuels': ['Coal', 'LPG/CNG/PNG/LNG Supplier']
        }
    },
    'Metals & Mining': {
        industries: ['Ferrous Metals', 'Non - Ferrous Metals', 'Mining & Minerals'],
        basicIndustries: {
            'Ferrous Metals': ['Iron & Steel', 'Iron & Steel Products'],
            'Non - Ferrous Metals': ['Aluminium', 'Copper', 'Zinc'],
            'Mining & Minerals': ['Mining']
        }
    },
    'Cement & Cement Products': {
        industries: ['Cement & Cement Products'],
        basicIndustries: {
            'Cement & Cement Products': ['Cement', 'Cement - Products']
        }
    },
    'Power': {
        industries: ['Power Generation', 'Power Distribution'],
        basicIndustries: {
            'Power Generation': ['Power Generation', 'Power Generation - Renewable'],
            'Power Distribution': ['Power - Transmission', 'Power - Distribution']
        }
    },
    'Capital Goods': {
        industries: ['Industrial Manufacturing', 'Construction'],
        basicIndustries: {
            'Industrial Manufacturing': ['Aerospace & Defense', 'Industrial Machinery'],
            'Construction': ['Civil Construction', 'Construction & Engineering']
        }
    },
    'Consumer Durables': {
        industries: ['Consumer Durables'],
        basicIndustries: {
            'Consumer Durables': ['Paints', 'Gems Jewellery And Watches', 'Consumer Electronics', 'White Goods']
        }
    },
    'Telecom': {
        industries: ['Telecom Services', 'Telecom Equipment'],
        basicIndustries: {
            'Telecom Services': ['Telecom - Services'],
            'Telecom Equipment': ['Telecom - Equipment & Accessories']
        }
    }
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { STOCK_UNIVERSE, SECTOR_MAPPING };
}
