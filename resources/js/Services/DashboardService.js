import axios from 'axios';

export async function fetchTopProducts() {
  const { data } = await axios.get('/api/dashboard/top-products');
  return data;
}

export async function fetchSalesTimeline() {
  const { data } = await axios.get('/api/dashboard/sales-timeline');
  return data;
}

export async function fetchPaymentMethods() {
  const { data } = await axios.get('/api/dashboard/payment-methods');
  return data;
}

export async function fetchDailyRevenue() {
  const { data } = await axios.get('/api/dashboard/daily-revenue');
  return data;
}
