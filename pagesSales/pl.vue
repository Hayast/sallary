<template>
	<view class="u-wrap" style="width:100%;">
		<view style="width:100%;">
			<u-row>
				<u-col span="10">
					<u-search placeholder="输入关键字" v-model="keyword" @change="doSearch2" @search="doSearch2"></u-search>
				</u-col>
				<u-col span="2">
					<view style="top:10px;" @tap="returnPage('/pagesSales/cart')">
						<u-icon name="shopping-cart" size="36" color="red" ></u-icon>
						<view class="num-box">
							 <u-badge numberType="overflow" :type="type" max="99" :value="cartNum"></u-badge>
						</view>
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="u-menu-wrap" style="width:100%;">
			<scroll-view scroll-y scroll-with-animation class="u-tab-view menu-scroll-view" :scroll-top="scrollTop"
				:scroll-into-view="itemId">
				<view v-for="(item,index) in typeLists" :key="index" class="u-tab-item"
					:class="[current == index ? 'u-tab-item-active' : '']" @tap.stop="swichMenu(index)">
					<text class="u-line-1">{{item.name}}</text>
				</view>
			</scroll-view>
			<scroll-view :scroll-top="scrollRightTop" scroll-y scroll-with-animation class="right-box"
				@scroll="rightScroll"  @scrolltolower="reachBottom">
				<view class="page-view">
					<view class="item-container">
						<view class="thumb-box" v-for="(item1, index1) in dataLists" :key="index1"
							@click="gotoDetial(item1)">
							<image class="item-menu-image" :src="globalInfo.Domain+'/images/'+item1.TPhoto" mode="">
							</image>
							<view class="item-menu-name">{{item1.TName}}</view>
						</view>
					</view>	
				</view>
				<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
			</scroll-view>
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
		astMp,
		getProduct,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			return {
				isLoading:false,
				cartNum:0,
				page:1,
				current:0,
				loadStatus: 'loadmore',
				keyword:'',
				queryInfo:{},
				typeLists:[],
				dataLists:[],
				arr: [],
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				admin: 'user/admin',
				globalInfo: 'global/get'
			})
		},
		onLoad(){
			//列子数据代替getParameter
			this.typeLists=this.globalInfo.ProductType
			console.log('this.globalInfo.ProductType',this.typeLists)
			this.getParameter(5); 
			this.doSearch();
		},
		onShow(){
			if(this.globalInfo.CurrProdType!=this.current){
				this.current=this.globalInfo.CurrProdType
				this.doSearch();
			}
			if(this.globalInfo.carts)
				this.cartNum=this.globalInfo.carts.length
			//console.log(this.current,this.globalInfo.CurrProdType)
		},
		onReady() {
			this.getMenuItemTop()
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setUserAdmin: 'user/setAdmin',
				setGlobalInfo: 'global/set'
			}),
			doSearch() {
				this.page = 1
				this.dataLists = []
				this.isLoading=false;
				setTimeout(() => {
					this.loadmore();
				}, 800);
			},
			doSearch2(){
				this.page = 1
				this.current=-1
				this.isLoading=false;
				this.dataLists = []
				setTimeout(() => {
					this.loadmore();
				}, 800);
			},
			returnPage(url) {
				console.log(url)
				uni.navigateTo({
					url
				})
			},
		
			reachBottom() {
				if (this.loadStatus == 'loading' || this.loadStatus == 'nomore') {
					return
				}
				this.loadStatus = "loading"
				setTimeout(() => {
					this.loadmore();
				}, 800);
			},
			async loadmore() {
				if(this.isLoading)
					return;
				this.isLoading=true;
				let params = {
					action:'getProduct',
					companyNo: this.userInfo.company_no,
					page: this.page,
					pageSize: 20,
					showDel:0,
					actType: 0,
					type: this.current,
					keyword: this.keyword
				}
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				let res = await astMp(params);
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
				this.scrollRightTop = this.oldScrollTop;
				this.$nextTick(function() {
					this.scrollRightTop = this.arr[this.current];
					this.leftMenuStatus(this.current);
				})
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				
				if (this.queryInfo.isLast) {
					this.loadStatus = 'nomore'
				}
				this.isLoading=false;
			},
			gotoDetial(item) {
				uni.navigateTo({
					url: `/pagesSales/pd?data=${encodeURIComponent(JSON.stringify(item))}`
					//url: `/pages/index/pd?id=${item.Id}`
				});
			},
			gotoPage(url) {
				console.log(url)
				uni.navigateTo({
					url
				})
			},
			// 点击左边的栏目切换
			async swichMenu(index) {
				this.current=index;
				if(this.globalInfo.CurrProdType!=this.current){
					this.setGlobalInfo({name:"CurrProdType",value:index})
					this.doSearch();
				}
			},
			// 获取一个目标元素的高度
			getElRect(elClass, dataVal) {
				new Promise((resolve, reject) => {
					const query = uni.createSelectorQuery().in(this);
					query.select('.' + elClass).fields({
						size: true
					}, res => {
						// 如果节点尚未生成，res值为null，循环调用执行
						if (!res) {
							setTimeout(() => {
								this.getElRect(elClass);
							}, 10);
							return;
						}
						this[dataVal] = res.height;
						resolve();
					}).exec();
				})
			},
			// 观测元素相交状态
			async observer() {
				this.tabbar.map((val, index) => {
					let observer = uni.createIntersectionObserver(this);
					// 检测右边scroll-view的id为itemxx的元素与right-box的相交状态
					// 如果跟.right-box底部相交，就动态设置左边栏目的活动状态
					observer.relativeTo('.right-box', {
						top: 0
					}).observe('#item' + index, res => {
						if (res.intersectionRatio > 0) {
							let id = res.id.substring(4);
							this.leftMenuStatus(id);
						}
					})
				})
			},
			// 设置左边菜单的滚动状态
			async leftMenuStatus(index) {
				this.current = index;
				// 如果为0，意味着尚未初始化
				if (this.menuHeight == 0 || this.menuItemHeight == 0) {
					await this.getElRect('menu-scroll-view', 'menuHeight');
					await this.getElRect('u-tab-item', 'menuItemHeight');
				}
				// 将菜单活动item垂直居中
				this.scrollTop = index * this.menuItemHeight + this.menuItemHeight / 2 - this.menuHeight / 2;
			},
			// 获取右边菜单每个item到顶部的距离
			getMenuItemTop() {
				new Promise(resolve => {
					let selectorQuery = uni.createSelectorQuery();
					selectorQuery.selectAll('.class-item').boundingClientRect((rects) => {
						// 如果节点尚未生成，rects值为[](因为用selectAll，所以返回的是数组)，循环调用执行
						if (!rects.length) {
							setTimeout(() => {
								this.getMenuItemTop();
							}, 10);
							return;
						}
						rects.forEach((rect) => {
							// 这里减去rects[0].top，是因为第一项顶部可能不是贴到导航栏(比如有个搜索框的情况)
							this.arr.push(rect.top - rects[0].top);
							resolve();
						})
					}).exec()
				})
			},
			// 右边菜单滚动
			async rightScroll(e) {
				this.oldScrollTop = e.detail.scrollTop;
				if (this.arr.length == 0) {
					await this.getMenuItemTop();
				}
				if (this.timer) return;
				if (!this.menuHeight) {
					await this.getElRect('menu-scroll-view', 'menuHeight');
				}
				setTimeout(() => { // 节流
					this.timer = null;
					// scrollHeight为右边菜单垂直中点位置
					let scrollHeight = e.detail.scrollTop + this.menuHeight / 2;
					for (let i = 0; i < this.arr.length; i++) {
						let height1 = this.arr[i];
						let height2 = this.arr[i + 1];
						// 如果不存在height2，意味着数据循环已经到了最后一个，设置左边菜单为最后一项即可
						if (!height2 || scrollHeight >= height1 && scrollHeight < height2) {
							this.leftMenuStatus(i);
							return;
						}
					}
				}, 10)
			},
			async getParameter(type) {
				let params = {
					page: 1,
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
					this.typeLists=data;
					console.log('this.typeLists',this.typeLists)
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.num-box{
		position: relative;
		width: 15px; 
		height: 15px;
		top:-35px;
		left:25px;
		background-color: #909193;
		color:red;
		border-radius: 15px;
	}
	.u-wrap {
		height: calc(100vh);
		/* #ifdef H5 */
		height: calc(100vh - var(--window-top));
		/* #endif */
		display: flex;
		flex-direction: column;
	}

	.u-search-box {
		padding: 18rpx 30rpx;
	}

	.u-menu-wrap {
		flex: 1;
		display: flex;
		overflow: hidden;
	}

	.u-search-inner {
		background-color: rgb(234, 234, 234);
		border-radius: 100rpx;
		display: flex;
		align-items: center;
		padding: 10rpx 16rpx;
	}

	.u-search-text {
		font-size: 26rpx;
		color: $u-tips-color;
		margin-left: 10rpx;
	}

	.u-tab-view {
		width: 200rpx;
		height: 100%;
	}

	.u-tab-item {
		height: 110rpx;
		background: #f6f6f6;
		box-sizing: border-box;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 26rpx;
		color: #444;
		font-weight: 400;
		line-height: 1;
	}

	.u-tab-item-active {
		position: relative;
		color: #000;
		font-size: 30rpx;
		font-weight: 600;
		background: #fff;
	}

	.u-tab-item-active::before {
		content: "";
		position: absolute;
		border-left: 4px solid;
		height: 32rpx;
		left: 0;
		top: 39rpx;
	}

	.u-tab-view {
		height: 100%;
	}

	.right-box {
		width:100%;
		background-color: rgb(250, 250, 250);
	}

	.page-view {
		padding: 16rpx;
	}

	.class-item {
		margin-bottom: 30rpx;
		background-color: #fff;
		padding: 16rpx;
		border-radius: 8rpx;
	}

	.class-item:last-child {
		min-height: 100vh;
	}

	.item-title {
		font-size: 26rpx;
		color: $u-main-color;
		font-weight: bold;
	}

	.item-menu-name {
		font-weight: normal;
		font-size: 24rpx;
		color: $u-main-color;
	}

	.item-container {
		display: flex;
		flex-wrap: wrap;
	}

	.thumb-box {
		width: 33.333333%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		margin-top: 20rpx;
	}

	.item-menu-image {
		width: 120rpx;
		height: 120rpx;
	}
</style>
