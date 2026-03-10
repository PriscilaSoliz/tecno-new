import axios from 'axios';

export async function fetchTopProducts(baseUrl = '') {
  const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
  const { data } = await axios.get(`${cleanBase}api/dashboard/top-products`);
  return data;
}

export async function fetchSalesTimeline(baseUrl = '') {
  const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
  const { data } = await axios.get(`${cleanBase}api/dashboard/sales-timeline`);
  return data;
}

export async function fetchPaymentMethods(baseUrl = '') {
  const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
  const { data } = await axios.get(`${cleanBase}api/dashboard/payment-methods`);
  return data;
}

export async function fetchDailyRevenue(baseUrl = '') {
  const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
  const { data } = await axios.get(`${cleanBase}api/dashboard/daily-revenue`);
  return data;
}
