import Vue from 'vue'
import App from './App'

// vuex
import store from './store'

// 引入全局uView
import uView from '@/uni_modules/uview-ui'

import mixin from './common/mixin'

Vue.prototype.$store = store

Vue.config.productionTip = false

App.mpType = 'app'
Vue.use(uView)

// #ifdef MP
// 引入uView对小程序分享的mixin封装
const mpShare = require('@/uni_modules/uview-ui/libs/mixin/mpShare.js')
Vue.mixin(mpShare)
// #endif

Vue.mixin(mixin)

const app = new Vue({
    store,
    ...App
})

// 引入请求封装
require('./util/request/index')(app)


Vue.prototype.PlayAudio = function(idx){
	var music = null;
	music = uni.createInnerAudioContext(); //创建播放器对象
	music.src= "/static/"+idx+".wav"; //选择播放的音频
	music.play(); //执行播放
	music.onEnded(() => {
		//播放结束
		music = null;
	});
}
Vue.prototype.getPostUrl = function(actStr = '', apiModule = '', isList = 0){
	let url = 'api/' + apiModule + '/' + actStr;
	    if (this.ApiLang == 1) {
	      if (actStr == '') actStr = 'get';
	      url = 'api/' + actStr + apiModule;
	    }
	    if (isList == 1) url = url + 'Lists';
	    console.log(url);
	    return url;
	}
Vue.prototype.FormatDt = function(dt){
	if(dt!=null && dt!=undefined)
		return dt.substring(0,10)	
}
Vue.prototype.getDtYm = function(dt){
	let res=dt.replace('-','');
	res=res.replace('/','').substr(0,6);
	return res;	
}

Vue.prototype.deepClone = function(val){
	return JSON.parse(JSON.stringify(val));
}

Vue.prototype.FormatNum = function(number,m=3){
	try{
		if(number==null || number==undefined)
			return 0;
		let result = Math.round(Math.pow(10, m) * number) / Math.pow(10, m);
		result = String(result);
		if (result.indexOf(".") == -1) {
		    if(m != 0){
		        result += ".";
		        result += new Array(m + 1).join('0');
		    }
		} else {
		    let arr = result.split('.');
		    if (arr[1].length < m) {
		        arr[1] += new Array(m - arr[1].length + 1).join('0')
		    }
		    result = arr.join('.')
		}
		return Number(result)
	}catch(e){
		return ''
	}
	    
};

// import escPrinter from '@/pagesPrinter/components/gprint/esc.js'
// import tscPrinter from '@/pagesPrinter/components/gprint/tsc.js'
// Vue.prototype.escPrinter = escPrinter
// Vue.prototype.tscPrinter = tscPrinter

import {BlePrinter} from '@/pagesPrinter/components/gprint/bleprinter.js'; //引入
Vue.prototype.BlePrinter = BlePrinter; 

Vue.prototype.monthDate = function(start,monthNum) {
	var now = new Date(); //当前日期 
	var nowYear = now.getFullYear(); //当前年
	var nowMonth = now.getMonth()+monthNum; //当前月 
	if(nowMonth===-1){
		nowYear=nowYear-1;
		nowMonth=11;
	}
	if(nowMonth===12){
		nowYear=nowYear+1;
		nowMonth=1;
	}
	
	//本月的开始时间
	var monthStartDate = new Date(nowYear, nowMonth, 1);
	//本月的结束时间
	var monthEndDate = new Date(nowYear, nowMonth + 1, 0);
	//var timeStar = Date.parse(monthStartDate) / 1000; //s
	//var timeEnd = Date.parse(monthEndDate) / 1000; //s
	var date = monthStartDate;
	if (start === 0) {
		date = monthEndDate;
	}
	var year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
		minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
		second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	day >= 0 && day <= 9 ? (day = "0" + day) : "";
	var timer = year + '/' + month + '/' + day;
	return timer;
};
Vue.prototype.now = function(isTime) {
	var date = new Date(),
		year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
		minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
		second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	day >= 0 && day <= 9 ? (day = "0" + day) : "";
	var timer = year + '/' + month + '/' + day;
	if (isTime === 1)
		timer = timer + ' ' + hour + ':' + minute + ':' + second;
	return timer;
	//return new Date().getTime()
}

Vue.prototype.CurrYearMonth = function(YearOrMonth) {
	var date = new Date(),
		year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
		minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
		second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	var Res=year;
	if(YearOrMonth==1)
	Res=month;
	return Res;
}

Vue.prototype.GetYear = function(val) {
	var date = new Date();
	var year = date.getFullYear()+val
	return year;
}

Vue.prototype.GetMonth = function() {
	var date = new Date();
	var month = date.getMonth()+1
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	return month;
}

Vue.prototype.CurrMonth = function(isTime) {
	var date = new Date(),
		year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
		minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
		second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	day >= 0 && day <= 9 ? (day = "0" + day) : "";
	var timer = year + '/' + month + '/' + day;
	if (isTime === 1)
		timer = timer + ' ' + hour + ':' + minute + ':' + second;
	return timer;
	//return new Date().getTime()
}

Vue.prototype.addDate = function(tDt, d, isTime) {
	var tempDate = new Date(tDt.replace(/-/g, "/")); //把日期字符串转换成日期格式
	var resultDate = new Date((tempDate / 1000 + (86400 * d)) * 1000); //增加n天后的日期
	var resultDateStr = resultDate.getFullYear() + "-" + (resultDate.getMonth() + 1) + "-" + (resultDate.getDate()); //将日期转化为字符串格式
	var date = new Date(resultDateStr),
		year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(),
		minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
		second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	month >= 1 && month <= 9 ? (month = "0" + month) : "";
	day >= 0 && day <= 9 ? (day = "0" + day) : "";
	var timer = year + '/' + month + '/' + day;
	if (isTime === 1)
		timer = timer + ' ' + hour + ':' + minute + ':' + second;
	return timer;
	//return new Date().getTime()
}

Vue.prototype.isArray = Array.isArray || function(obj) {
	return obj instanceof Array
}
app.$mount()
