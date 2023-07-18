<template>
	<view class="carClass">
		<view class="header">
			<view class="row-l-r">
				<view class="title-text">购物车</view>
				<view>
				</view>
			</view>
		</view>
		<view v-if="dataLists.length==0">
			<view class="u-font-14" style="color:#2B85E4;">
				还没有选购产品
			</view>
			<image style="width: 100%; height: 260px;" mode="widthFix" src="/static/icon/kongche.png"></image>
			<u-button size="medium" type="warning" @click="returnPage('')">去逛逛</u-button>
		</view>
				
		<view v-else class="goodsClass">
			<view style="background-color: white;">
				<view class="row-l-r">
				<view></view>
				<view>
					可用金额:{{canAmt}}元
				</view>
			</view>
			</view>
			<u-swipe-action :show="item.show" :index="index" v-for="(item, index) in dataLists" :key="index"
				@click="click" @open="open" :options="options">
				<u-row>
					<view class="chengboxs">
						<u-checkbox-group v-model="ugb[index]" :wrap="true">
							<u-checkbox shape="circle" @change="selectProduct(index)" v-model="item.selected"
								:key="index" :name="index">
							</u-checkbox>
						</u-checkbox-group>
					</view>
					<view class="item u-border-bottom">
						<u-row>
							<u-col span="3">
								<image mode="aspectFill" :src="globalInfo.Domain+'/'+item.img" />
							</u-col>
							<u-col span="9">
								<view class="title-wrap">
									<text class="title u-line-2">{{ item.name }}</text>
									<text>{{ item.color }} {{ item.size }}</text>
								</view>
								<view class="footerClass">
									<text class="price">￥{{ item.price }}</text>
									<view class="footerBtn">
										<u-number-box :index="item.id" v-model="item.quantity" :positive-integer="true"
											:input-height="46" :input-width="45" :min="1" @blur="changeQty(index)"
											@minus="sub(index)" @plus="add(index)"></u-number-box>
									</view>
								</view>
							</u-col>
						</u-row>
					</view>
				</u-row>

			</u-swipe-action>
		</view>
		<view v-if="dataLists.length>0" class="submitBar">
			<view class="left">
				<u-checkbox-group name="ugb2" v-model="ugb2v">
					<u-checkbox shape="circle" v-model="checkedAll" @change="selectAll()" name="ugball" label="全选"></u-checkbox>
				</u-checkbox-group>
			</view>
			<view class="right">
				<u-row>
					<u-col span="8">
						<text class="total-price">合计：
							<text class="money">￥{{sumPrice}}</text>
						</text>
					</u-col>
					<u-col span="3">
						<u-button class="jsBtn" size="mini" @click="addOrder()" type="warning">
							结算({{selectedAllRowLength}})
						</u-button>
					</u-col>
				</u-row>

			</view>
		</view>

		<u-toast ref="uToast" />
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		saveCartOrder
	} from '@/common/api.js';
	export default {
		data() {
			return {
				canAmt:0,
				selectedAllRowLength:0,
				sumPrice: 0.00,
				ugb:[],
				checkboxValue:[],
				ugb2v:[],
				allSelectProduct: 0,
				checkedAll: false,
				disabled: false,
				btnWidth: 180,
				show: false,
				options: [
					{
						text: '删除',
						style: {
							backgroundColor: '#dd524d'
						}
					}
				],
				checkboxSkus: [],
				showNull: true,
				firmName: '全部',
				dtIdx: 0,
				labelWidth: 80,
				current: 0,
				actType: 0,
				itemIdx: 0,
				showScroll: 0,
				itemRecord: {},
				keyword: '',
				dataLists: [],
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			if (uni.$u.mpShare) {
				uni.$u.mpShare.title = this.globalInfo.MpShareTitle;
				let sharePath = this.globalInfo.MpSharePath + '?Domain=' + this.globalInfo.Domain;
				if (this.hasLogin) {
					uni.$u.mpShare.title = this.userInfo.company_name + this.globalInfo.MpShareTitle;
					sharePath = sharePath + '&TUserId1=' + this.userInfo.company_no;
				}
				uni.$u.mpShare.path = sharePath;
			}
			let dataList = this.globalInfo.carts || []
			this.dataLists = JSON.parse(JSON.stringify(dataList))
			if (this.dataLists.length > 0)
				this.showNull = false
			console.log(this.dataLists)
		},
		onShow(){
			this.updateAccount();
		},
		onReady() {
			// 微信小程序需要用此写法
			//this.$refs.dtP1.setFormatter(this.formatter)
			//this.$refs.dtP2.setFormatter(this.formatter)
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			returnPage(url) {
				let pages = getCurrentPages(); // 当前页面        
				let beforePage = pages[pages.length - 3]; // 上上一页        
				//beforePage.$vm.reFresh = Math.random()//触发上一页监听器            
				uni.navigateBack({
					delta: 1, //返回上上一级注意这里要设置为2
					success: function() {
						//beforePage.$vm.reFresh();
						console.log("返回成功!");
					}
				})
				if (url != '') {
					uni.navigateTo({
						url
					})
				}

			},
			async updateAccount(){
				if(!this.hasLogin){
					return;
				}
				let params={
					companyNo: this.userInfo.company_no,
					action:'getCurrentUserInfo',
					uid:this.userInfo.uid
				}
				let res=await astCf(params);
				if (res==undefined)
					return;
				if(res.code==0){
					if(!res.userInfo || res.userInfo==undefined){
						return
					}
					let userInfo=res.userInfo;
					let copanyInfo=res.company;
					let n1=userInfo.TNum3||0;
					if(isNaN(n1))
						n1=0;
					let n2=copanyInfo.TNum3||0;
					if(isNaN(n2))
						n2=0;
					this.canAmt=n1+n2;
				}
			},
			changeQty(changeNumber, m, n) {
				this.dataLists[m].quantity = changeNumber
				this.sumTotalPrice()
			},

			sub(m, n) {
				if (this.dataLists[m].quantity <= 1) {
					return
				}
				this.dataLists[m].quantity--
				console.log('******', this.dataLists[m].quantity)

				this.sumTotalPrice()
			},

			/**
			 * 数字步进 加
			 * @param {Object} m 父 索引
			 * @param {Object} n 子 索引
			 */
			add(m, n) {
				console.log('******', this.dataLists[m].quantity)
				this.dataLists[m].quantity++
				console.log('******', this.dataLists[m].quantity)
				this.sumTotalPrice()
			},

			selectAll() {
				let checkboxValue=[]
				this.sumPrice = 0
				this.checkedAll = !this.checkedAll
				this.ugb=[]
				this.selectedAllRowLength = 0
				for (let i = 0; i < this.dataLists.length; i++) {
					this.dataLists[i].selected = this.checkedAll
					this.ugb.push([])
					if(this.dataLists[i].selected){
						this.ugb[i].push(i)
						//checkboxValue.push(i)
						this.sumPrice += this.dataLists[i].price * this.dataLists[i].quantity
						this.selectedAllRowLength += this.dataLists[i].quantity
					}
				}
				this.sumPrice = this.sumPrice.toFixed(2)
				console.log(this.ugb2v)
				//this.checkboxValue=checkboxValue
				
				// setTimeout(()=>{		//添加定时器 转为异步
				// 			this.show3=true	
				// 		})
			},

			/**
			 * 选择单个商品
			 * @param {Object} m 父 索引
			 * @param {Object} n 子 索引
			 */
			selectProduct(idx) {
				this.dataLists[idx].selected = !this.dataLists[idx].selected
				console.log(this.checkboxValue)
				// 循环设置父全选 如果全部选中  则父全选选中 否则 不选中
				// for (let i = 0; i < this.dataLists[idx].length; i++) {
				// 	if (this.dataLists[idx].goods[i].selected) {
				// 		this.dataLists[idx].selectedAll = true
				// 	} else {
				// 		this.dataLists[idx].selectedAll = false
				// 		break
				// 	}
				// }
				//this.computeSelectedAll()
				this.sumTotalPrice()
			},


			// 全选所有行
			selectedAllRow() {
				//this.isSelectedAllRow = this.dataLists.length ? !this.isSelectedAllRow : false
				let len = this.dataLists.length
				for (let i = 0; i < len; i++) {
					this.dataLists[i].selected = true
				}
				this.sumTotalPrice()
			},


			// 合计总价格
			sumTotalPrice() {
				// 获取选择信息
				this.sumPrice = 0
				this.selectedAllRowLength = 0
				let isAllChecked=true
				for (let i = 0; i < this.dataLists.length; i++) {
					if (this.dataLists[i].selected) {
						this.sumPrice += this.dataLists[i].price * this.dataLists[i].quantity
						this.selectedAllRowLength += this.dataLists[i].quantity
					}else{
						isAllChecked=false
					}
				}
				this.checkedAll=isAllChecked
				if(isAllChecked)
					this.ugb2v=["ugball"]
				else
					this.ugb2v=[]
				this.sumPrice = this.sumPrice.toFixed(2)
			},
			showMsg(msg) {
				uni.showToast({
					title: msg,
					icon: 'none'
				});
			},
			async addOrder(){
				this.sumTotalPrice()
				let orderList=[]
				if(this.selectedAllRowLength<=0){
					this.showMsg('请先选购商品.')
					return
				}
				/*TProductGuid string             `json:"TProductGuid" bson:"TProductGuid"`
				TGuid        string             `json:"TGuid" bson:"TGuid"`
				TKeyGuid     string             `json:"TKeyGuid" bson:"TKeyGuid"`
				TName        string             `json:"TName" bson:"TName"`
				TQuantity    float64            `json:"TQuantity" bson:"TQuantity"`
				TPrice       float64            `json:"TPrice" bson:"TPrice"`
				TAmt         float64            `json:"TAmt" bson:"TAmt"`
				TColor       string             `json:"TColor" bson:"TColor"`
				TSize        string             `json:"TSize" bson:"TSize"`
				TAttr        string             `json:"TAttr" bson:"TAttr"`
				TDt          string             `json:"TDt" bson:"TDt"`
				TDtUpdate    string             `json:"TDtUpdate" bson:"TDtUpdate"`
				TRemark      string             `json:"TRemark" bson:"TRemark"`
				TIsDelete    int64              `json:"TIsDelete" bson:"TIsDelete"`
				*/
				for (let i = 0; i < this.dataLists.length; i++) {
					if (this.dataLists[i].selected) {
						orderList.push({TProductGuid:this.dataLists[i].TGuid,
							TGuid:this.dataLists[i].TGuid,
							TPhoto:this.dataLists[i].img,
							TQuantity:this.dataLists[i].quantity*1,
							TPrice:this.dataLists[i].price*1,
							TAmt:this.dataLists[i].quantity*this.dataLists[i].price,
							TName:this.dataLists[i].name,
							TColor:this.dataLists[i].color,
							TSize:this.dataLists[i].size,
							TIsDelete:0
						})
					}
				}
				
				let ItemRecord={TCorpGuid:0,TQuantity:this.selectedAllRowLength*1,TAmt:this.sumPrice*1,	TIsDelete:0,TUserId:this.globalInfo.DefaultUser.uid,TDetailLists:orderList}	
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					ioType:0,
					ItemRecord				
				}
				if(this.canAmt<this.sumPrice){
					this.showMsg('请先充值.')
					return;
				}
				console.log(params,this.globalInfo.DefaultUser)
				let res = await saveCartOrder(params)
				this.setGlobalInfo({
					name: "carts",
					value: []
				})
				this.returnPage('/pagesSales/myorder')
			},


			/* 二位数组复制 */
			copyTowArr(m, n) {
				let arr = {}
				for (let key in this.dataLists[m]) {
					arr[key] = this.dataLists[m][key]
				}
				arr.goods = []
				arr.goods.push(this.dataLists[m])
				return arr
			},

			/* 删除数据从对象中 */
			deleteDataFromObj() {
				//
			},
			/* 将商品移出购物出 */
			removeGoodsEvent() {
				uni.showLoading()
				// 请求接口写在此位置
				this.deleteDataFromObj()
				setTimeout(function() {
					uni.hideLoading()
				}, 800)
			}
		},
		navigateTo(url) {
			uni.navigateTo({
				url
			})
		},
		hideKeyboard() {
			uni.hideKeyboard()
		}
	}
</script>

<style>
	/* #ifndef H5 */
	page {
		width: 100%;
		height: 100%;
		background-color: #f2f2f2;
	}

	/* #endif */
</style>
<style lang="scss">
	.tips {
		position: relative;
		z-index: 1;
		text-align: center;
		
		.tips-img {
			display: block;
			margin: 250rpx auto 0;
		}
	}
	
		
	.carClass {
		width: 100%;
		height: 100%;
	}

	.goodsClass {
		width: 100%;
		height: 100%;
		margin-bottom: 120rpx;
	}

	.noData {
		padding-top: 50%;
		text-align: center;
	}

	.item {
		display: flex;
		padding: 20rpx 20rpx 20rpx 10rpx;
	}

	image {
		width: 120rpx;
		flex: 0 0 120rpx;
		height: 150rpx;
		margin-right: 30rpx;
		border-radius: 12rpx;
	}

	.title-wrap {
		text-align: left;
		font-size: 28rpx;
		color: $u-content-color;
		margin-top: 0rpx;
	}

	.title {
		text-align: left;
		font-size: 28rpx;
		color: $u-content-color;
		margin-top: 0rpx;
	}

	.chengboxs {
		width: 60rpx;
		padding: 66rpx 0 0 29rpx;
		height: 150rpx;
		float: left;
	}

	.footerClass {
		width: 93.5%;
		display: flex;
		top: 100rpx;
		left: 155rpx;
		height: 56rpx;

		.price {
			font-size: 26rpx;
			color: #ff1030;
			width: 80rpx;
			height: 56rpx;
			line-height: 56rpx;
			padding-right: 10rpx;
		}

		.mPrice {
			font-size: 24rpx;
			color: #a2a2a2;
			width: 80rpx;
			height: 56rpx;
			line-height: 56rpx;
			text-decoration: line-through;
		}

		.footerBtn {
			// padding-left:170rpx;
			padding-left: 96rpx;
			display: flex;
			margin-top: 40rpx;

			.btn {
				width: 50rpx;
				height: 50rpx;
			}

			.inputClass {
				width: 50rpx;
				height: 50rpx;
				padding: 0rpx !important;
			}
		}

	}

	.submitBar {
		display: inline-flex;
		position: fixed;
		border: solid 2rpx #f2f2f2;
		background-color: #ffffff;
		padding: 16rpx;
		bottom: 10rpx;
		border-radius: 16rpx;
		width: 100%;

		.left {
			width: 23%;
			padding-left: 20rpx;
		}

		.right {
			width: 75%;
			padding-top: 6rpx;

			.total-price {
				.money {
					font-weight: 600;
				}
			}

			.jsBtn {
				float: right;
			}
		}
	}
</style>
