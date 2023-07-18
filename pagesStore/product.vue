<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">产品管理</view>
				<view v-if="showPage==0" style="width:160upx;">
						<view style="width:80upx;float: left;">
							<u-icon name="search" color="#0989de" size="36" @click="showFilter=true"></u-icon>
						</view>
						<view style="width:80upx;">
							<u-icon v-if="canEdit" name="plus-circle-fill" color="#2775f3" size="36" @click="add()"></u-icon>
						</view>
				</view>
				<view v-if="showPage==1">
					<u-icon name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row"
						:class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="9">
								<view v-if="canEdit" @click="edit(item)">
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TName}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TName}}
								</view>
							</u-col>
							<u-col span="3">
								{{typeLists[0][item.TType||0].name || '未设置'}}
							</u-col>
						</u-row>
						<u-row>
							<u-col span="5">
								<span style="margin-left:10rpx;">
									{{item.TQuantity || ''}}
								</span>
							</u-col>
							<u-col span="3">
								{{item.TNum1 || ''}}
							</u-col>
							<u-col span="3">
								{{item.TStyle || ''}}
							</u-col>
							<u-col span="1" @click="edit(item)">
								<u-icon name="edit-pen-fill" color="#2979ff" size="24"></u-icon>
							</u-col>
						</u-row>

					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				<view class="page-box" v-if="showPage==1">
					<view>
						<u--form>
							<u-form-item label="编号:" :label-width="labelWidth" borderBottom>
								<view>
									{{itemRecord.TGuid}}
								</view>
							</u-form-item>
							<u-form-item label="名称:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TName" border="none" placeholder="输入产品名称"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="款号:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TStyle" border="none" placeholder="输入款号"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="单价:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TPrice" border="none" placeholder="输入产品单价" type="number"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item :labelWidth="labelWidth" label="类型" borderBottom
								@click="showType=true;showActType=0; hideKeyboard()">
								{{typeLists[0][itemRecord.TType||0].name || ''}}
								<u-icon slot="right" name="arrow-right"></u-icon>
							</u-form-item>
							<u-form-item :labelWidth="labelWidth" label="图片" borderBottom>
								<u-upload
										:fileList="fileList1"
										@afterRead="afterRead"
										@delete="deletePic"
										name="1"
										multiple
										:maxCount="1"
										
									></u-upload>
							</u-form-item>
							
							<u-form-item :labelWidth="labelWidth" label="SKU列表" borderBottom>
								...
								<u-icon v-if="canEdit" slot="right" name="plus-circle-fill" @click="addItem(0)"></u-icon>
							</u-form-item>
							<view v-for="(item, index) in itemRecord.TSkuLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="1"><span class="text-no">{{index+1}}.</span></u-col>
									<u-col span="3">
										<u--input v-model="item.TGuid" border="none" placeholder="条码"></u--input>
									</u-col>
									<u-col span="2">
										<u--input type="text" v-model="item.TPrice" border="none" placeholder="价格"></u--input>
									</u-col>
									<u-col span="2">
										<u--input type="text" v-model="item.TSize" border="none" placeholder="规格"></u--input>
									</u-col>
									<u-col span="2">
										<u--input type="text" v-model="item.TColor" border="none" placeholder="属性"></u--input>
									</u-col>
									<u-col span="1">
										{{item.TQuantity}}
									</u-col>
									<u-col span="1">
										<u-icon v-if="canEdit" name="trash-fill" color="#ff5500" size="24" @click="deleteItem(index)"></u-icon>
									</u-col>
								</u-row>
							</view>
							<view v-if="showExt">
								
								<u-form-item v-if="showPLists[7].TCheckedP" :label="showPLists[7].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt1" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[8].TCheckedP" :label="showPLists[8].TName" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TExt2" border="none" 
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[9].TCheckedP" :label="showPLists[9].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt3" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[10].TCheckedP" :label="showPLists[10].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt4" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[11].TCheckedP" :label="showPLists[11].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt5" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[12].TCheckedP" :label="showPLists[12].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt6" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[13].TCheckedP" :label="showPLists[13].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt7" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[14].TCheckedP" :label="showPLists[14].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt8" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[15].TCheckedP" :label="showPLists[15].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt9" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[16].TCheckedP" :label="showPLists[16].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TExt10" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[17].TCheckedP" :label="showPLists[17].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum1" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[18].TCheckedP" :label="showPLists[18].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum2" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[19].TCheckedP" :label="showPLists[19].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum3" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[20].TCheckedP" :label="showPLists[20].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum4" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[21].TCheckedP" :label="showPLists[21].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum5" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								<u-form-item v-if="showPLists[22].TCheckedP" :label="showPLists[22].TName" :label-width="labelWidth" borderBottom>
									<u--input v-model="itemRecord.TNum6" border="none"
										:customType="inputType"></u--input>
								</u-form-item>
								
							</view>
							
						</u--form>
					</view>
					<view>
						<view class="custom-btn">
							<u-button v-if="canEdit" type="primary"  @click="save(actType)">储存</u-button>
						</view>
						<view v-if="itemRecord._id!=''" class="custom-btn">
							<u-button v-if="canEdit" type="warning" :class="custom-btn" @click="save(4)">删除</u-button>
						</view>
					</view>

				</view>
			</scroll-view>

			<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelDelete" @confirm="save(2)" ref="uModal" :async-close="false"
				cancel-text="取消" confirm-text="确认删除">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<text class="font-red">操作删除后记录无法恢复. </text>
				</view>
			</u-modal>

			<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
				:async-close="false" cancel-text="重设" confirm-text="搜索">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
							<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
						</u-form-item>
						
						<u-form-item :labelWidth="labelWidth" label="类型" borderBottom
							@click="showType=true;showActType=1; hideKeyboard()">
							<u--input v-model="typeName" disabled disabledColor="#ffffff" placeholder="请选择类型"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>		
					</u-form>
				</view>
			</u-modal>
			<u-picker
				:show="showSize"
				:columns="sizeLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showColor"
				:columns="colorLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showType"
				:columns="typeLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			
		</view>
		<view v-if="isShow">
		    <page-container :show="isShow" :overlay="false" @beforeleave="beforeleave"></page-container>
		</view>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		saveProduct,
		getProduct,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				isShow: true,
				fileList1: [],
				token:'',
				domain:'',
				labelWidth: 100,
				current: 0,
				canEdit: false,
				canApply: false,
				userType:0,
				showPage: 0,
				showActType: 0,
				itemIdx: 0,
				dept: 0,
				deptName: '',
				type: -1,
				typeName: '',
				itemRecord: {},
				keyword: '',
				//minDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -730))),
				//maxDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), 365))),
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showFilter: false,
				showColor: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showSize: false,
				showType: false,
				sn: '',
				style: '',
				sizeLists: [{
					name: '请选择',
					sort: 0
				}],
				colorLists: [{
					name: '请选择',
					sort: 0
				}],
				
				typeLists: [[{
						name: '请选择',
						value: 0
					}
				]],
				showPLists:[],
				tabValue: 0,
				loadStatus: 'loadmore',
				showExt:false,
				page: 1,
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputType:'backgroundColor:#fff;margin:4rpx;'
				inputType: '',
				customBtn: {
					color: 'red',
					margin: '30rpx'
				}

			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				adminWrite: 'user/adminWrite',
				adminApply: 'user/adminApply',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			this.urlLists = this.globalInfo.UrlLists
			var pages = getCurrentPages() //获取加载的页面
			var currentPage = pages[pages.length - 1] //获取当前页面的对象
			var url = "/" + currentPage.route //当前页面url
			for (var i = 0; i < this.urlLists.length; i++) {
				if (this.urlLists[i].url == url) {
					this.tabValue = i;
					break;
				}
			}
			let idxAdmin=13;
			this.canEdit=this.adminWrite[idxAdmin];
			this.canApply=this.adminApply[idxAdmin];
			this.getParameter(2)
			this.getParameter(3)
			this.getParameter(5)
			this.getParameter(111)
			this.loadmore();
			this.token=this.userInfo.token
			this.domain=this.globalInfo.Domain
			//console.log('-----------------',this.token)
		},
		onReady() {
			// 微信小程序需要用此写法
			////this.$refs.dtP1.setFormatter(this.formatter)
			////this.$refs.dtP2.setFormatter(this.formatter)
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			beforeleave(){
				if(this.showPage==1){
					this.isShow = false;  //这个很重要，一定要先把弹框删除掉
					uni.showModal({
						title: "确定要取消储存并离开吗?",
						success: (e)=>{
							if(e.confirm) {
								//判断是上一个页面进入(返回)，还是直接进入这个页面(回首页)
								this.showPage=0;
							}else {
								//点取消，生成新的弹框
								this.showPage=1;
							}
							this.isShow = true;
						}
					})
				}
			},
			// 删除图片
			deletePic(event) {
				this[`fileList${event.name}`].splice(event.index, 1)
			},
			// 新增图片
			async afterRead(event) {
				// 当设置 mutiple 为 true 时, file 为数组格式，否则为对象格式
				
				let lists = [].concat(event.file)
				let fileListLen = this[`fileList${event.name}`].length
				lists.map((item) => {
					this[`fileList${event.name}`].push({
						...item,
						status: 'uploading',
						message: '上传中'
					})
				})
				for (let i = 0; i < lists.length; i++) {
					const result = await this.uploadFilePromise(lists[i].url)
					let photoInfo=JSON.parse(result);
					//console.log(photoInfo,photoInfo.filePath)
					if(photoInfo.code==0){
						this.itemRecord.TPhoto=photoInfo.filePath
					}
					//console.log('event.name',event.name)
					let item = this[`fileList${event.name}`][fileListLen]
					this[`fileList${event.name}`].splice(fileListLen, 1, Object.assign(item, {
						status: 'success',
						message: '',
						url: photoInfo.filePath
					}))
					fileListLen++
				}
			},
			checkFileSize(file){
				let that = this;
				  if (file === undefined) {
					return;
				  }
				  if (file.size / 1024 > 500) {
					// 文件大于1M（根据需求更改），进行压缩上传
					this.photoCompress(
					  file,
					  {
						// 调用压缩图片方法
						quality: 0.2,
					  },
					  function (base64Codes) {
						// console.log("压缩后：" + base.length / 1024 + " " + base);
						let bl = that.base64UrlToBlob(base64Codes);
						// file.append('file', bl, 'file_' + Date.parse(new Date()) + '.jpg') // 文件对象
						//that.uploadLice(bl); // 请求图片上传接口
						return bl;
					  }
					);
				  } else {
					// 小于等于1M 原图上传
					//this.uploadLice(file);
					return file;
				  }
				let lists = [].concat(event.file)
				let fileListLen = this[`fileList${event.name}`].length
				lists.map((item) => {
					this[`fileList${event.name}`].push({
						...item,
						status: 'uploading',
						message: '上传中'
					})
				})
			},
			/**
			 * base64 转 Blob 格式 和file格式
			 */
			base64UrlToBlob(urlData) {
			  let arr = urlData.split(","),
				mime = arr[0].match(/:(.*?);/)[1], // 去掉url的头，并转化为byte
				bstr = atob(arr[1]), // 处理异常,将ascii码小于0的转换为大于0
				n = bstr.length,
				u8arr = new Uint8Array(n);
			  while (n--) {
				u8arr[n] = bstr.charCodeAt(n);
			  }
			  // 转blob
			  // return new Blob([u8arr], {type: mime})
			  let filename = Date.parse(new Date()) + ".jpg";
			  // 转file
			  return new File([u8arr], filename, { type: mime });
			},
			/*
			  压缩图片
			  file：文件(类型是图片格式)，
			  obj：文件压缩后对象width， height， quality(0-1)
			  callback：容器或者回调函数
			*/
			photoCompress(file, obj, callback) {
			  let that = this;
			  let ready = new FileReader();
			  /* 开始读取指定File对象中的内容. 读取操作完成时,返回一个URL格式的字符串. */
			  ready.readAsDataURL(file);
			  ready.onload = function () {
				let re = this.result;
				that.canvasDataURL(re, obj, callback); // 开始压缩
			  };
			},
			/* 利用canvas数据化图片进行压缩 */
			/* 图片转base64 */
			canvasDataURL(path, obj, callback) {
			  let img = new Image();
			  img.src = path;
			  img.onload = function () {
				let that = this; // 指到img
				// 默认按比例压缩
				let w = that.width,
				  h = that.height,
				  scale = w / h;
				w = obj.width || w;
				h = obj.height || w / scale;
				let quality = 0.2; // 默认图片质量为0.7
				// 生成canvas
				let canvas = document.createElement("canvas");
				let ctx = canvas.getContext("2d");
				// 创建属性节点
				let anw = document.createAttribute("width");
				anw.nodeValue = w;
				let anh = document.createAttribute("height");
				anh.nodeValue = h;
				canvas.setAttributeNode(anw);
				canvas.setAttributeNode(anh);
				ctx.drawImage(that, 0, 0, w, h);
				// 图像质量
				if (obj.quality && obj.quality >= 1 && obj.quality < 0) {
				  quality = obj.quality;
				}
				// quality值越小，所绘制出的图像越模糊
				let base64 = canvas.toDataURL("image/jpeg", quality);
				// 回调函数返回base64的值
				callback(base64);
			  };
			},
			uploadFilePromise(url) {
				return new Promise((resolve, reject) => {
					//console.log( this.token)
					let uploadApi = this.domain+'/api/upload';
					let a = uni.uploadFile({
						url: uploadApi, // 仅为示例，非真实的接口地址
						filePath: url,
						name: 'file',
						header: { Authorization: 'Bearer ' + this.token },
						formData: {
							user: 'test'
						},
						success: (res) => {
							setTimeout(() => {
								//console.log(res.data.filePath);
								resolve(res.data)
							}, 1000)
						},fail:(err)=>{
							console.log(err);
						}
					});
				})
			},

			reachBottom() {
				if (this.loadStatus == 'loading' || this.loadStatus == 'nomore') {
					return
				}
				this.loadStatus = "loading"
				setTimeout(() => {
					this.loadmore();
				}, 1200);

			},
			doSearch() {
				this.page = 1
				this.showFilter = false
				this.dataLists = []
				this.loadmore()
			},
			add(){
				this.actType=0;
				this.itemIdx = this.dataLists.length
				this.itemRecord = {_id:'',TGuid:'',TType:0,TName:'',TStyle:'',TUnit:'',TQuantity:0,TPhoto:'',TSkuLists:[]}
				this.showPage = 1
			},
			edit(item, itemIdx) {
				this.actType=1;
				this.itemIdx = itemIdx
				this.itemRecord = item
				this.showPage = 1
				if(this.itemRecord.TSkuLists==undefined)
					this.itemRecord.TSkuLists=[];
				if(this.itemRecord.TPhoto){
					this.fileList1=[{url:this.domain+'/'+ this.itemRecord.TPhoto}];
				}
			},
			addItem(idx){
				let listT=[]
				let k=0;
				let len=this.itemRecord.TSkuLists.length
				if(len==0){
					listT.push({TGuid:'',TPrice:0,TColor:'',TSize:'',TQuantity:0})
				}else{
					for(let i=0;i<len;i++){
						k=k+1
						//this.itemRecord.TSkuLists[i].TSn=k
						listT.push(this.itemRecord.TSkuLists[i])
						if(i==idx){
							k=k+1
							listT.push({TGuid:'',TPrice:0,TColor:'',TSize:'',TQuantity:0})
						}
					}
				}
				
				this.itemRecord.TSkuLists=listT
			},
			deleteItem(idx){
				//this.delLists.push(this.itemRecord.TSkuLists[idx])
				this.itemRecord.TSkuLists.splice(idx,1)
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let params = {
					page: this.page,
					companyNo: this.userInfo.company_no,
					showDel:0,
					//tokenYun:this.userInfo.token,
					actType: 0,
					type: this.type||-1,
					keyword: this.keyword
				}
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//post wu {}
				let res = await getProduct({params});
				let data = res.dataLists
				if(!data){
					uni.hideLoading();
					uni.showToast({
						title:'出错了...'
					})
					return;
				}
				this.queryInfo = res.queryInfo
				data.forEach(item => {
					this.dataLists.push(item)
				});
				// for (let i = 0; i < data.length; i++) {
				// 	this.dataLists.push(data[i])
				// }
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				
				if (this.queryInfo.isLast) {
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter() {
				this.dept = ''
				this.type = -1
				this.showFilter = false
			},
			async save(actType) {
				if (actType == 4) {
					this.showDel = true
					return
				} else {
					this.showDel = false
				}
				
				let params = {
					actType: actType,
					companyNo: this.userInfo.company_no,
					uid: this.globalInfo.DefaultUser.uid,
					//tokenYun:this.userInfo.token,
					itemRecord: this.itemRecord
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				let res = await saveProduct(params);
				uni.hideLoading();
				if (actType == 2)
					this.dataLists.splice(this.itemIdx, 1)
				if (actType == 0)
					this.doSearch()
				this.showPage = 0
			},
			async configDelete() {
				this.save(2)
			},
			cancelDelete() {
				this.showDel = false
			},
			navigateTo(url) {
				//uni.redirectTo({
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				uni.switchTab({
					url
				})
			},
			changeTabbar(e) {
				this.tabValue = e
				var url = this.urlLists[e].url
				uni.redirectTo({
					url
				})
			},
			close() {
				if (this.current == 1)
					this.showDt1 = false
				if (this.current == 2)
					this.showDt2 = false
			},
			cancel(idx) {
				this.close()
			},
			confirm(e) {
				this.close()
				const timeFormat = uni.$u.timeFormat
				if (this.current == 1) {
					//this.dt1 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt1 = timeFormat(e.value, 'yyyy/mm/dd')
				}
				if (this.current == 2) {
					//this.dt2 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt2 = timeFormat(e.value, 'yyyy/mm/dd')
				}

			},
			change(e) {
				//
			},
			formatter(type, value) {
				if (type === 'year') {
					return `${value}年`
				}
				if (type === 'month') {
					return `${value}月`
				}
				if (type === 'day') {
					return `${value}日`
				}
				return value
			},
			
			async getParameter(type) {
				let params = {
					page: 1,
					companyNo: this.userInfo.company_no,
					resType: 0,
					pageSize: 200,
					type: type,
					ordBy: 'TSort'
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				let res = await getParameter({
					params
				});
				//let data = res.dataLists
				uni.hideLoading();
				if(type==111){
					this.showPLists=res.dataLists
					if (this.showPLists.length>10){
						if (this.showPLists[0].TCheckedP){
							this.showExt=true
							while(this.showPLists.length<23){
								this.showPLists.push({
									TChecked: false,
									TCheckedP: false,
									TName: '',
									TSort: 0,
									TValue: 0
								})
							}
						}
					//console.log('============== showPLists =================',this.showPLists)
					}
				}
				if (type == 2) {
					this.colorLists = []
					let data=[{
						name: '请选择',
						sort: 0
					}]
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						data.push({
							name: item.TName,
							sort: item.TSort
						})
					}
					this.colorLists.push(data)
				}
				if (type == 3) {
					this.sizeLists = []
					let data=[{
						name: '请选择',
						sort: 0
					}]
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						data.push({
							name: item.TName,
							sort: item.TSort
						})
					}
					this.sizeLists.push(data)
				}
				if (type == 5) {
					this.typeLists = []
					let data=[{
						name: '请选择',
						sort: 0
					}]
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						data.push({
							name: item.TName,
							sort: item.TSort
						})
					}
					this.typeLists.push(data)
				}
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},		
			selectPicker(e) {
				let item = e.value[0];
				if(this.showSize){
					if(this.showActType==1){
						//this.dept = item.sort
						//this.deptName = item.name
					}else{
						this.itemRecord.TSize=item.name
					}
				}
				if(this.showType){
					if(this.showActType==1){
						this.type = item.sort
						this.typeName = item.name
					}else{
						this.itemRecord.TType=item.sort
					}
				}
				this.cancelPicker();
			},
			confirmPicker(e) {
				this.selectPicker(e)
			},
			cancelPicker() {
				this.showSize = false
				this.showType = false
			},
			changeHandlerPicker(e) {
				this.changePicker(e)
				const {
					columnIndex,
					value,
					values,
					index,
					// 微信小程序无法将picker实例传出来，只能通过ref操作
					picker = this.$refs.uPicker3
				} = e
				if (columnIndex === 0) {
					//多列
					//picker.setColumnValues(1, this.columnData[index])
				}
			}
		}
	}
</script>

<style lang="scss">
	.item-row {
		padding: 6rpx;
	}
</style>
