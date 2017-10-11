import axios from 'axios'
let base = 'http://kwen.t.weixinren.cn/admin';
// let base = 'http://mylinux1.com/admin';

export const uploadUrl = base + '/Content/upload';
export const uploadFileUrl = base + '/Content/uploadFile';
export const downloadUrl = base + '/Order/download';

export const requestLogin = params => { return axios.post(`${base}/public/checkLogin`, params).then(res => res.data); }
export const requestLogout = params => { return axios.get(`${base}/public/logout`, params).then(res => res.data); }

export const getUserList = params => { return axios.get(`${base}/user/list`, { params: params }); }
export const getUserListPage = params => { return axios.get(`${base}/user/listpage`, { params: params }); }
export const removeUser = params => { return axios.get(`${base}/user/remove`, { params: params }); }
export const batchRemoveUser = params => { return axios.get(`${base}/user/batchremove`, { params: params }); }
export const editUser = params => { return axios.get(`${base}/user/edit`, { params: params }); }
export const addUser = params => { return axios.get(`${base}/user/add`, { params: params }); }

export const GoodsList = params => { return axios.post(`${base}/product/productList`, params).then(res => res.data); }
export const getProductInfo = params => { return axios.post(`${base}/product/getProductInfo`, params).then(res => res.data); }
export const renewStatus = params => { return axios.post(`${base}/product/renewStatus`, params).then(res => res.data); }
export const changeOff = params => { return axios.post(`${base}/product/changeOff`, params).then(res => res.data); }
export const addProduct = params => { return axios.post(`${base}/product/addProduct`, params).then(res => res.data); }
export const editProduct = params => { return axios.post(`${base}/product/editProduct`, params).then(res => res.data); }

export const SupplyList = params => { return axios.post(`${base}/Supply/findGoods`, params).then(res => res.data); }
export const SupplyReview = params => { return axios.post(`${base}/Supply/review`, params).then(res => res.data); }
export const SupplyDetail = params => { return axios.post(`${base}/Supply/details`, params).then(res => res.data); }

export const DemandList = params => { return axios.post(`${base}/BuyOffer/findGoods`, params).then(res => res.data); }
export const DemandReview = params => { return axios.post(`${base}/BuyOffer/review`, params).then(res => res.data); }
export const DemandDetail = params => { return axios.post(`${base}/BuyOffer/details`, params).then(res => res.data); }

export const getAreaList = params => { return axios.post(`${base}/product/getArea`, params).then(res => res.data); }
export const getBrandList = params => { return axios.post(`${base}/product/getBrand`, params).then(res => res.data); }
export const getCategoryList = params => { return axios.post(`${base}/product/getCategory`, params).then(res => res.data); }
export const getProducerList = params => { return axios.post(`${base}/product/getProducer`, params).then(res => res.data); }
export const getIndicatorList = params => { return axios.post(`${base}/product/getIndicator`, params).then(res => res.data); }
export const getCompanyModelList = params => { return axios.post(`${base}/product/getModel`, params).then(res => res.data); }
export const getQualityGrade = params => { return axios.post(`${base}/product/getQualityGrade`, params).then(res => res.data); }
export const getWeightUnit = params => { return axios.post(`${base}/product/getWeightUnit`, params).then(res => res.data); }

export const getNewsList = params => { return axios.post(`${base}/Content/newsList`, params).then(res => res.data); }
export const editNews = params => { return axios.post(`${base}/Content/news`, params).then(res => res.data); }
export const deleteNews = params => { return axios.post(`${base}/Content/delNews`, params).then(res => res.data); }
export const partnerList = params => { return axios.post(`${base}/Content/partnerList`, params).then(res => res.data); }
export const addpartner = params => { return axios.post(`${base}/Content/addpartner`, params).then(res => res.data); }
export const deletePartner = params => { return axios.post(`${base}/Content/delpartner`, params).then(res => res.data); }
export const aboutUs = params => { return axios.post(`${base}/Content/aboutUs`, params).then(res => res.data); }
export const editAboutUs = params => { return axios.post(`${base}/Content/about`, params).then(res => res.data); }
export const editProtocol = params => { return axios.post(`${base}/Content/protocol`, params).then(res => res.data); }
export const getProtocol = params => { return axios.post(`${base}/Content/getProtocol`, params).then(res => res.data); }
export const editContact = params => { return axios.post(`${base}/Content/contact`, params).then(res => res.data); }
export const editCooperate = params => { return axios.post(`${base}/Content/cooperate`, params).then(res => res.data); }

export const getmemberList = params => { return axios.post(`${base}/member/memberList`, params).then(res => res.data); }
export const memberOperate = params => { return axios.post(`${base}/member/memberOperate`, params).then(res => res.data); }
export const memberOperateDel = params => { return axios.post(`${base}/member/memberOperateDel`, params).then(res => res.data); }

export const DataCategory = params => { return axios.post(`${base}/Data/getCategory`, params).then(res => res.data); }
export const indicatorList = params => { return axios.post(`${base}/Data/indicatorList`, params).then(res => res.data); }
export const modelList = params => { return axios.post(`${base}/Data/modelList`, params).then(res => res.data); }

export const inquiryList = params => { return axios.post(`${base}/Inquiry/getList`, params).then(res => res.data); }
export const inquiryDetail = params => { return axios.post(`${base}/Inquiry/getDetail`, params).then(res => res.data); }
export const postQuotes = params => { return axios.post(`${base}/Inquiry/postQuotes`, params).then(res => res.data); }
export const getMessageList = params => { return axios.post(`${base}/Inquiry/getMessageList`, params).then(res => res.data); }
export const sendMessage = params => { return axios.post(`${base}/Inquiry/sendMessage`, params).then(res => res.data); }

export const ajaxWriteOrder = params => { return axios.post(`${base}/Order/ajaxWriteOrder`, params).then(res => res.data); }
export const postOrder = params => { return axios.post(`${base}/Order/postOrder`, params).then(res => res.data); }
export const getOrderList = params => { return axios.post(`${base}/Order/getOrderList`, params).then(res => res.data); }
export const editOrder = params => { return axios.post(`${base}/Order/editOrder`, params).then(res => res.data); }
export const getOrderDetail = params => { return axios.post(`${base}/Order/getOrderDetail`, params).then(res => res.data); }
export const uploadContract = params => { return axios.post(`${base}/Order/uploadContract`, params).then(res => res.data); }
export const confirmOrder = params => { return axios.post(`${base}/Order/confirmOrder`, params).then(res => res.data); }
export const changeOrderState = params => { return axios.post(`${base}/Order/changeOrderState`, params).then(res => res.data); }
export const uploadOrderFile = params => { return axios.post(`${base}/Order/uploadOrderFile`, params).then(res => res.data); }
