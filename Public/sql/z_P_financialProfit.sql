ALTER PROCEDURE [dbo].[zz_testtest]
	@pingtai VARCHAR(50)='',
	@DateFlag int =1,                         --时间标记 0 交易时间  1 发货时间 （默认是发货时间）
	@BeginDate	varchar(20),               --开始时间
	@EndDate	Varchar(20),               --结束时间
	@SalerAliasName VARCHAR(max) = '',     --卖家简称 可以用逗号分隔
  @Saler VARCHAR(max)='',							   --销售员 可以用逗号分隔
  @StoreName VARCHAR(max)='',							--出货仓库
	@RateFlag  int =0  ,                      --是否使用订单保存的汇率 1 使用  0 使用汇率表里的汇率
  @ExchangeRate2 FLOAT(50) = 6,						--汇率默认是6.0
	@PageNum int = 10,            					 --每页记录数
	@PageIndex int = 1             			--页码

AS
begin

SET NOCOUNT ON  -- 调试数据，暂时注释掉
declare
	@PageCount int =10,          --输出页数
	@RecCount int=0           --输出记录数
 -- 存放订单副表信息
   create table #tempskucostprice(
  PackFee money default 0,
  goodcostprice money default 0,
  costprice money default 0,
  tradenid int default 0,
  StoreName varchar(200) default '')
  -- 创建临时表用来存储信息
  create Table #TempTradeInfo
  (
    nid int,                                 --nid
	OrderDay varchar(20),                    --时间显示到日(yyyy-mm-dd) 0 交易 1 发货
	OrderMonth varchar(20),                  --时间显示到月(yyyy-mm) 0 交易 1 发货
 -- TRANSACTIONTYPE VARCHAR(20),   --销售渠道
	Suffix varchar(100),                     --卖家简称
	ExchangeRate float default 0,            --销售汇率 包括(销售价,PP手续费,买家付运费) 根据系统参数取 系统汇率还是订单里面的汇率
	SaleMoney float default 0,               --销售价 AMT
	SHIPPINGAMT  float default 0,            --买家付运费
	ppFee  float default 0,                  --PP手续费
	ebayExchangeRate float,                  --平台手续费汇率 ebay paypal的取美元  否则 根据系统参数取 系统汇率还是订单里面的汇率
	eBayFee  float default 0,                --平台手续费
	CostMoney  float default 0,              --成本价￥  dt表的商品成本加一起（根据系统参数取商品成本还是库存平均价）
	ExpressFare  float default 0,            --快递费￥
	InpackageMoney  float default 0,         --内包装金额￥
	OutpackageMoney  float default 0,        --外包装金额￥
	TotalWeight  float default 0,            --总重量 G
	DevDate dateTime null,                   --最大商品开发时间
	ItemCostPrice float default 0,           --商品信息的成本价（商品成本加一起）
	ACK VARCHAR(50),                         --店铺单号
	[User] VARCHAR(100),                     --卖家id
	PaiDanMen VARCHAR(50),                   --派单人
	SKU varchar(8000),                       --sku明细
	TrackNo varchar(200),                    --跟踪号
	CurrencyCode varchar(10),                --币种
	wlWay Varchar(200),                      --物流方式名称
	SHIPTOCOUNTRYNAME varchar(100),          --收货人国家
	TRANSACTIONID varchar(50),               --PP交易ID
	BuyerID nvarchar(120),                   --买家ID
	SaleItemsCount NUMERIC(10,0) DEFAULT 0,  --销售总数量
	SaleSKUCount NUMERIC(10,0) DEFAULT 0,    --SKU数量
	SHIPTOCOUNTRYCODE varchar(100),          --收件人国家代码
	CountryZnName  varchar(100),              --收件人国家中文名
	StoreName varchar(200) -- 仓库名称
  )
  --临时的NID
  create Table #TempTradeInfo_TempNID
  (
    nid int,                                 --nid
    primary key(nid)
  )
  --最终的NID
  create Table #TempTradeInfo_NID
  (
    nid int primary key(nid)
  )

  --分解卖家简称放到表格里
   CREATE TABLE #tbSalerAliasName( SalerAliasName VARCHAR(100))
  IF LTRIM(RTRIM(@SalerAliasName)) <> ''
  BEGIN
	DECLARE @sSQLCmd VARCHAR(max) = ''
	SET @SalerAliasName = REPLACE(@SalerAliasName,',','''))UNION SELECT ltrim(rtrim(''')
    SET @sSQLCmd = 'INSERT INTO #tbSalerAliasName(SalerAliasName) SELECT ltrim(rtrim('+ @SalerAliasName+'))'
	EXEC(@sSQLCmd )

  END

--分解销售员到临时表里

CREATE TABLE #tbSaler (Saler varchar(100))
 IF LTRIM(RTRIM(@Saler)) <> ''
  BEGIN
	DECLARE @sSQLCmd2 VARCHAR(max) = ''
	SET @Saler = REPLACE(@Saler,',','''))UNION SELECT ltrim(rtrim(''')
    SET @sSQLCmd2 = 'INSERT INTO #tbSaler(Saler) SELECT ltrim(rtrim('+ @Saler+'))'
	EXEC(@sSQLCmd2)

  END

--分解发货仓库 到临时表
  CREATE TABLE #tbStoreName(StoreName VARCHAR(100))
  IF LTRIM(RTRIM(@StoreName)) <> ''
  BEGIN
	DECLARE @sSQLCmd3 VARCHAR(max) = ''
	SET @StoreName = REPLACE(@StoreName,',','''))UNION SELECT ltrim(rtrim(''')
    SET @sSQLCmd3 = 'INSERT INTO #tbStoreName(StoreName) SELECT ltrim(rtrim('+ @StoreName+'))'
	EXEC(@sSQLCmd3)
  END


  --查找USD的汇率
  Declare
	@ExchangeRate float
  set
	@ExchangeRate =ISNULL((select ExchangeRate from B_CurrencyCode where CURRENCYCODE='USD'),0)
  if @ExchangeRate=0
    set @ExchangeRate=1

  --查找成本计价方法
  Declare
	@CalcCostFlag int
  set
	@CalcCostFlag =ISNULL((select ParaValue from B_SysParams where ParaCode ='CalCostFlag'),0)



  --数据插入临时表 --正常表
  --先查询主表条件里面的订单编号
  insert into #TempTradeInfo_TempNID
  select
    m.NID
  from P_Trade(nolock) m
  where
    ((@DateFlag=1 and m.FilterFlag=100 and  convert(varchar(10),m.CLOSINGDATE,121) between @BeginDate and @endDate)
	 or (@DateFlag=0 and convert(varchar(10),DATEADD(HOUR,8,m.ORDERTIME),121) between @BeginDate and @endDate))
	AND (ISNULL(@SalerAliasName,'') = '' OR m.SUFFIX IN (SELECT SalerAliasName FROM #tbSalerAliasName))    --卖家简称

--	AND (ISNULL(@eBayUserID,'') = '' OR m.[User] = @eBayUserID) 	                                       --卖家ID
	--AND (ISNULL(@chanel,'') = '' OR isnull(m.TRANSACTIONTYPE,'') = @chanel)                                --销售渠道
	--AND (ISNULL(@SaleType,'') = '' OR isnull(m.SALUTATION,'') = @SaleType) 			                       --销售类型
--	AND (@fno = '' OR cast(m.nid as varchar(50)) = @fno or m.ACK=@fno)                                     --订单号或店铺单号

  --再查询最终的明细的订单编号
  insert into #TempTradeInfo_NID
  select
    D.TradeNid
  from (select TradeNid,SKU from P_TradeDt(nolock) where TradeNid in (select NID from #TempTradeInfo_TempNID)) d
  left join B_GoodsSKU(nolock) bgs on bgs.SKU = d.SKU
  left join b_goods(nolock) bg on bg.NID = bgs.GoodsID
         --销售员Saler
     group by
    D.TradeNid
      -- 正常表 sku数据
    insert into #tempskucostprice(tradenid,goodcostprice,costprice,PackFee,StoreName)
    select
    dt.TradeNID,
    sum(dt.L_QTY*(case when bgs.costprice<>0 then bgs.costprice
	                                      else bg.CostPrice end)),
	SUM(dt.CostPrice),
	sum(dt.L_QTY*(ISNULL(bg.PackFee,0))),
	MAX(bs.StoreName)
    from P_TradeDt dt with(nolock)
    inner join B_GoodsSKU bgs with(nolock)  on bgs.SKU = dt.SKU
	inner join b_goods bg with(nolock)  on bg.NID = bgs.GoodsID
	left join B_Store bs on bs.NID=dt.StoreID
    where dt.tradenid in (select NID from #TempTradeInfo_NID)
    group by dt.TradeNID
  print 1
  --再根据查询的NID插入详细信息
  insert into #TempTradeInfo
  select
	m.nid,                                                                                --nid
	case when @DateFlag=0  then convert(varchar(10),DATEADD(HOUR,8,m.ORDERTIME),121)
	     else convert(varchar(10),m.CLOSINGDATE,121) end as OrderDay,	                  --时间显示到日(yyyy-mm-dd) 0 交易 1 发货
	case when @DateFlag=0  then convert(varchar(7),DATEADD(HOUR,8,m.ORDERTIME),121)
	     else convert(varchar(7),m.CLOSINGDATE,121) end as OrderMonth,                    --时间显示到月(yyyy-mm) 0 交易 1 发货
--m.TRANSACTIONTYPE,  	   --销售渠道
	m.Suffix,	                                                                          --卖家简称

	case when @RateFlag = 1 then isnull(m.EXCHANGERATE,1)
	     else isnull(c.ExchangeRate,1) end as ExchangeRate,                               --销售汇率 包括(销售价,pp手续费,买家付运费)
        --else  c.ExchangeRate=6.8 end as ExchangeRate,
	m.AMT as SaleMoney,			                                                              --销售价 AMT
	m.SHIPPINGAMT as SHIPPINGAMT,		                                                      --买家付运费
	m.FEEAMT as ppFee ,                                                                   --PP手续费
	case when m.ADDRESSOWNER = 'ebay' or m.ADDRESSOWNER = 'paypal' then @ExchangeRate
		 else case when @RateFlag = 1 then isnull(m.EXCHANGERATE,1)
		           else isnull(c.ExchangeRate,1) end end as ebayExchangeRate,             --平台手续费汇率
	m.SHIPDISCOUNT as eBayFee,		                                                      --平台手续费
	isnull( (case when @CalcCostFlag =0 then ts.costprice else ts.goodcostprice end),0) as CostMoney,                                    --成本价￥  dt表的商品成本加一起（根据系统参数取商品成本还是库存平均价）
	m.ExpressFare,                                                                        --快递费￥
	isnull(ts.PackFee,0) as InpackageMoney,                               --内包装金额￥
	m.INSURANCEAMOUNT as outpackageMoney,	                                              --外包装金额￥
	m.TotalWeight*1000 as TotalWeight,                                                    --总重量 G
	'' as DevDate,                                              --最大商品开发时间
	isnull(ts.goodcostprice,0) as ItemCostPrice,                                --商品信息的成本价（商品成本加一起）
	m.ACK,                                                                                --店铺单号
	m.[User],	                                                                          --卖家id
	m.PaiDanMen,	                                                                      --派单人
	m.AllGoodsDetail as sku,	                                                          --sku明细
	m.TrackNo,                                                                            --跟踪号
	m.CURRENCYCODE as CurrencyCode,                                                       --币种
	l.name as wlWay,                                                                      --物流方式名称
	m.SHIPTOCOUNTRYNAME,                                                                  --收货人国家
	m.TRANSACTIONID,		                                                              --PP交易ID
	m.BuyerID,                                                                            --买家ID
	m.MULTIITEM AS SaleItemsCount,			                                              --销售总数量
	m.SALESTAX AS SaleSKUCount,                                                           --SKU数量
	m.SHIPTOCOUNTRYCODE,		                                                          --收件人国家代码
	bc.CountryZnName,	                                                                  --国家中文名
	ts.StoreName                                                                          -- 仓库名称
  from
	P_Trade(nolock) m
	left outer join B_LogisticWay(nolock) l on l.NID=m.logicsWayNID
	left outer join B_CurrencyCode(nolock) c on c.CURRENCYCODE=m.CURRENCYCODE
	left join B_Country(nolock) bc on bc.CountryCode=m.SHIPTOCOUNTRYCODE
	left join #tempskucostprice ts with(nolock) on ts.tradenid=m.NID
  where
	m.NID in (select NID from #TempTradeInfo_NID)


  --清除不用的数据
  Truncate Table #TempTradeInfo_TempNID
  Truncate Table #TempTradeInfo_NID
  truncate table #tempskucostprice

  --数据插入临时表 --历史表
  --先查询主表条件里面的订单编号
  insert into #TempTradeInfo_TempNID
  select
    m.NID
  from P_Trade_his(nolock) m
  where
    ((@DateFlag=1 and  convert(varchar(10),m.CLOSINGDATE,121) between @BeginDate and @endDate)
	 or (@DateFlag=0 and convert(varchar(10),DATEADD(HOUR,8,m.ORDERTIME),121) between @BeginDate and @endDate))
	AND (ISNULL(@SalerAliasName,'') = '' OR m.SUFFIX IN (SELECT SalerAliasName FROM #tbSalerAliasName))    --卖家简称
--	AND (ISNULL(@eBayUserID,'') = '' OR m.[User] = @eBayUserID) 	                                       --卖家ID
	--AND (ISNULL(@chanel,'') = '' OR isnull(m.TRANSACTIONTYPE,'') = @chanel)                                --销售渠道
 			                       --销售类型
	--AND (@fno = '' OR cast(m.nid as varchar(50)) = @fno or m.ACK=@fno)                                     --订单号或店铺单号

  --再查询最终的明细的订单编号
  insert into #TempTradeInfo_NID
  select
    D.TradeNid
  from (select TradeNid,SKU from P_TradeDt_his(nolock) where TradeNid in (select NID from #TempTradeInfo_TempNID)) d
  left join B_GoodsSKU(nolock) bgs on bgs.SKU = d.SKU
  left join b_goods(nolock) bg on bg.NID = bgs.GoodsID
  group by
    D.TradeNid

     -- 历史表sku数据
    insert into #tempskucostprice(tradenid,goodcostprice,costprice,PackFee,StoreName)
    select
    dt.TradeNID,
    sum(dt.L_QTY*(case when bgs.costprice<>0 then bgs.costprice
	                                      else bg.CostPrice end)),
	SUM(dt.CostPrice),
	sum(dt.L_QTY*(ISNULL(bg.PackFee,0))),
	MAX(bs.StoreName)
    from P_TradeDt_His dt with(nolock)
    inner join B_GoodsSKU bgs with(nolock)  on bgs.SKU = dt.SKU
	inner join b_goods bg with(nolock)  on bg.NID = bgs.GoodsID
	left join B_Store bs on bs.NID=dt.StoreID
    where dt.tradenid in (select NID from #TempTradeInfo_NID)

    group by dt.TradeNID

  --再根据查询的NID插入详细信息
  insert into #TempTradeInfo
  select
	m.nid,                                                                                --nid
	case when @DateFlag=0  then convert(varchar(10),DATEADD(HOUR,8,m.ORDERTIME),121)
	     else convert(varchar(10),m.CLOSINGDATE,121) end as OrderDay,	                  --时间显示到日(yyyy-mm-dd) 0 交易 1 发货
	case when @DateFlag=0  then convert(varchar(7),DATEADD(HOUR,8,m.ORDERTIME),121)
	     else convert(varchar(7),m.CLOSINGDATE,121) end as OrderMonth,                    --时间显示到月(yyyy-mm) 0 交易 1 发货
	--m.TRANSACTIONTYPE, 	   --销售渠道
	m.Suffix,	                                                                          --卖家简称
	case when @RateFlag = 1 then isnull(m.EXCHANGERATE,1)
	     else isnull(c.ExchangeRate,1) end as ExchangeRate,                               --销售汇率 包括(销售价,pp手续费,买家付运费)
    m.AMT as SaleMoney,			                                                          --销售价 AMT
	m.SHIPPINGAMT as SHIPPINGAMT,		                                                  --买家付运费
	m.FEEAMT as ppFee ,                                                                   --PP手续费
	case when m.ADDRESSOWNER = 'ebay' or m.ADDRESSOWNER = 'paypal' then @ExchangeRate
	     else case when @RateFlag = 1 then isnull(m.EXCHANGERATE,1)
	     else isnull(c.ExchangeRate,1) end end as ebayExchangeRate,             --平台手续费汇率

	m.SHIPDISCOUNT as eBayFee,		                                                      --平台手续费
	isnull((case when @CalcCostFlag =0 then ts.costprice
	               else ts.goodcostprice end),0) as CostMoney,        --成本价￥  dt表的商品成本加一起（根据系统参数取商品成本还是库存平均价）
	m.ExpressFare,                                                                        --快递费￥ 运费成本
	isnull(ts.PackFee,0) as InpackageMoney,   --内包装金额￥
	m.INSURANCEAMOUNT as outpackageMoney,	                                              --外包装金额￥
	m.TotalWeight*1000 as TotalWeight,                                                    --总重量 G
	'' as DevDate,                   --最大商品开发时间
	isnull(ts.goodcostprice,0) as ItemCostPrice,    --商品信息的成本价（商品成本加一起）
	m.ACK,                                                                                --店铺单号
	m.[User],	                                                                          --卖家id
	m.PaiDanMen,	                                                                      --派单人
	m.AllGoodsDetail as sku,	                                                          --sku明细
	m.TrackNo,                                                                            --跟踪号
	m.CURRENCYCODE as CurrencyCode,                                                       --币种
	l.name as wlWay,                                                                      --物流方式名称
	m.SHIPTOCOUNTRYNAME,                                                                  --收货人国家
	m.TRANSACTIONID,		                                                              --PP交易ID
	m.BuyerID,                                                                            --买家ID
	m.MULTIITEM AS SaleItemsCount,			                                              --销售总数量
	m.SALESTAX AS SaleSKUCount,                                                           --SKU数量
	m.SHIPTOCOUNTRYCODE,		                                                          --收件人国家代码
	bc.CountryZnName,	                                                                  --国家中文名
	ts.StoreName                                                                          -- 仓库名称
  from
	P_Trade_his(nolock) m
	left outer join B_LogisticWay(nolock) l on l.NID=m.logicsWayNID
	left outer join B_CurrencyCode(nolock) c on c.CURRENCYCODE=m.CURRENCYCODE
	left join B_Country(nolock) bc on bc.CountryCode=m.SHIPTOCOUNTRYCODE
	left join #tempskucostprice ts with(nolock) on ts.tradenid=m.NID
  where
	m.NID in (select NID from #TempTradeInfo_NID)


--SELECT top 100 * FROM #TempTradeInfo
	--按月份
	--统计信息
	--扣除PP手续费 = 销售价 - pp手续费
	--实得金额 = 销售价 - pp手续费 - 平台手续费
	--包装成本 = 内包装 + 外包装
	--利润 =  销售价 - pp手续费 - 平台手续费 - 商品成本价 - 内包装 - 外包装 - 快递费
	--利润率 = 利润 / 销售价
  --毛利 = 成交价-所有费用
SELECT * INTO #grossProfit from (select
	 --f.OrderMonth as OrderDay,	                                             --时间-月
		--f.TRANSACTIONTYPE,
    sp.pingtai,
	  f.Suffix,                                                                --卖家简称
	  ss.salesman,
   cast(round(sum(f.SaleMoney * f.ExchangeRate / @ExchangeRate),2)   as   numeric(20,2)) as SaleMoney, 												--销售价$ --成交价$  SaleMoney

   cast(round(sum(f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2),2)   as   numeric(20,2))  as SaleMoneyzn,  		--销售价￥ 就是成交价￥，按汇率变化变化
	 cast(round(sum(f.eBayFee * f.ebayExchangeRate / @ExchangeRate),2)   as   numeric(20,2)) as eBayFeeebay,               			--平台交易费$ --ebay成交费$

	 cast(round(sum(f.eBayFee * f.ebayExchangeRate / @ExchangeRate * @ExchangeRate2),2)   as   numeric(20,2)) as eBayFeeznebay,   --平台交易费￥					 --ebay成交费￥
	 cast(round(sum(f.ppFee * f.ExchangeRate / @ExchangeRate),2)    as   numeric(20,2)) as ppFee,                        					--pp手续费$  --平台/PP交易费￥
	 --cast(round(sum(f.ppFee * f.ExchangeRate),2)   as   numeric(20,2)) as ppFeezn,
	 cast(round(sum(f.ppFee * f.ExchangeRate / @ExchangeRate * @ExchangeRate2),2)   as   numeric(20,2)) as ppFeezn, --pp手续费￥						 --平台/PP交易费$
	 cast(round(sum(f.CostMoney),2)  as   numeric(20,2)) as CostMoney,                                           --商品成本价￥					 --商品成本￥
   round(sum(f.ExpressFare),2) as ExpressFare,                                       --快递费￥	运费成本
	 cast(round(sum(f.inpackageMoney),2)   as   numeric(20,2)) as inpackageMoney                              --内包装成本￥					 --内包装成本￥
	,isnull(f.StoreName,'未知')	as StoreName															--出货仓

--,round(sum(f.SHIPPINGAMT * f.ExchangeRate/ @ExchangeRate * @ExchangeRate2),2) as SHIPPINGAMTzn                      --买家付运费￥ --2017年03-28修改
--,(case when (sum(f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2))=0 then 0 else (f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2)/(sum(f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2))  end) as rate
 -- ,(f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2)/(sum(f.SaleMoney * f.ExchangeRate / @ExchangeRate* @ExchangeRate2)) as rate
from #TempTradeInfo f
		LEFT JOIN Y_suffixPingtai sp ON  f.suffix=sp.suffix
		LEFT JOIN Y_SuffixSalerman ss ON ss.suffix=f.suffix
	  group by
		f.Suffix,ss.salesman,sp.pingtai,f.StoreName--,f.salemoney,f.ExchangeRate
) haha--,f.TRANSACTIONTYPE,f.OrderMonth
 WHERE
(ISNULL(@Saler,'') = '' OR haha.salesman IN (SELECT Saler FROM #tbSaler))     --销售员
 AND (ISNULL(@StoreName,'') = '' OR  haha.StoreName IN (SELECT StoreName FROM #tbStoreName))    --发货仓库
AND (ISNULL(@pingtai,'') = '' OR  haha.pingtai LIKE  '%'+@pingtai+'%')     --平台

--select * from  #grossProfit

-- 按仓库名称和销售额计算死库占比系数 加了 SaleMoneyznrate
select
g.suffix,
sum(g.saleMoneyzn) as totalmoney
into #suffixTotal
from  #grossprofit g GROUP BY g.suffix

--#SaleMoneyznrate 按成交价的占比存放的表
select gpt.suffix
,gpt.SaleMoneyzn
,gpt.storename
,stl.totalmoney
,(case when stl.totalmoney=0 then 0 else cast(round(gpt.SaleMoneyzn/stl.totalmoney,2) as NUMERIC(20,2) )end ) as SaleMoneyznrate
INTO  #SaleMoneyznrate
from #grossprofit as gpt
LEFT JOIN #suffixTotal as stl ON gpt.suffix=stl.suffix order by gpt.suffix



--存放按时间段 账号分组后的 杂费 是$ 为单位  要按汇率准换成￥
select
e.accountName,
sum(e.insertionfee*@ExchangeRate2) as insertionfee  INTO #ebayInsertionFee
from ebayInsertionFee e
WHERE convert(varchar(10),createdDay,121) BETWEEN @BeginDate and @EndDate
group BY e.accountName

--线上清仓费 变成死库费用
SELECT suffix,SUM(diefeeZn) as diefeeZn
INTO #tempofflineclearn
FROM(SELECT
suffix, diefeeZn,ClearanceDate
FROM Y_offlineclearn
WHERE CONVERT(varchar(10),ClearanceDate,121) BETWEEN @BeginDate and @EndDate) dd
GROUP BY suffix

--运营杂费(侵权损失)
SELECT suffix,SUM(saleOpeFeeZn) as saleOpeFeeZn
INTO #tempOpeFee
FROM(
SELECT
suffix,saleOpeFeeZn,saleOpeTime
FROM Y_saleOpeFee
WHERE CONVERT(varchar(10),saleOpeTime,121) BETWEEN @BeginDate and @EndDate) pp
GROUP BY suffix




--退款费用

/*
select TransactionID,
			sum(netValue*bcode.ExchangeRate)/(SELECT ExchangeRate from B_CurrencyCode where CURRENCYCODE='USD')as netValue,
		max(currencyID) as currencyID, max(DATEADD(hour, 8,transactionTime)) as transactionTime
into #tmprefund
 FROM Y_PayPalRefund as yrd LEFT JOIN B_CurrencyCode as bcode on bcode.currencycode =yrd.currencyID

 group by TransactionID

create table #tmporders(TransactionID varchar(50), suffix varchar(30))
insert into #tmporders select pt.transactionid,pt.suffix  from P_trade as pt where
--DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), getdate()) >= DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), '2017-01-01')
 addressowner='ebay'--

-- 缺货单
insert into #tmporders select pt.transactionid,pt.suffix  from P_tradeun as pt where
--DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), getdate()) >= DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), '2017-01-01')
 addressowner='ebay'  and  pt.filterflag=1
-- 已归档订单

insert into #tmporders select pt.transactionid,pt.suffix  from P_trade_his as pt where
--DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), getdate()) >= DATEDIFF(day, DATEADD(hour, 8, pt.ordertime), '2017-01-01')
 addressowner='ebay'

select sum(netvalue) as netValue,suffix,convert(varchar(10),transactionTime,121) as transactionTime into #finalRefund
from #tmprefund as red LEFT JOIN #tmporders as tos on red.transactionid = tos.transactionid
where tos.transactionid is not null
group by suffix,convert(varchar(10),transactionTime,121) order by transactionTime desc
*/

-- 2017.03.14 修改paypal退款的匹配方法

select TransactionID,
sum(netValue*bcode.ExchangeRate)/(SELECT ExchangeRate from B_CurrencyCode where CURRENCYCODE='USD')as netValue,
max(currencyID) as currencyID, max(DATEADD(hour, 8,transactionTime)) as transactionTime
into #tmprefund
 FROM Y_PayPalRefund as yrd LEFT JOIN B_CurrencyCode as bcode on bcode.currencycode =yrd.currencyID

 group by TransactionID
create table #finalRefund(TransactionID varchar(50), suffix varchar(30),netValue DECIMAL(10,2), transactionTime datetime)


-- 正常单
insert into #finalRefund
select tps.transactionid,pt.suffix,tps.netvalue,tps.transactionTime from #tmprefund as tps LEFT JOIN P_Trade as pt on tps.transactionid = pt.transactionid
where pt.transactionid is not null

--归档订单
union
select tps.transactionid,pt.suffix,tps.netvalue,tps.transactionTime  from #tmprefund as tps LEFT JOIN P_Trade_his as pt on tps.transactionid = pt.transactionid where pt.transactionid is not null

--缺货订单
union
select tps.transactionid,pt.suffix,tps.netvalue,tps.transactionTime from #tmprefund as tps LEFT JOIN P_Tradeun as pt on tps.transactionid = pt.transactionid where
pt.PROTECTIONELIGIBILITYTYPE  not in ('取消订单', '其它异常单') and pt.suffix   is not null


-- 2017-03-33 加上合并订单
union
select tps.transactionid,pt.suffix,tps.netvalue,tps.transactionTime from #tmprefund as tps LEFT JOIN P_Trade_b as pt on tps.transactionid = pt.transactionid

where pt.transactionid is not null




--select * from #finalRefund where transactiontime BETWEEN '2017-02-01'and '2017-03-01'



-- wish 退款费用

create table #wishOrders (suffix varchar(30) ,ack varchar(50),total_value DECIMAL(10,2), refund_time datetime)
insert into #wishOrders
select notename,order_id,total_value,refund_time  from  Y_wish_refunded as ywr LEFT JOIN p_trade as ptd on ywr.order_id=ptd.ack where ptd.suffix  is not null
UNION
select notename,order_id,total_value,refund_time  from  Y_wish_refunded as ywr LEFT JOIN p_tradeun as ptd on ywr.order_id=ptd.ack where  ptd.PROTECTIONELIGIBILITYTYPE  not in ('取消订单', '其它异常单') and ptd.suffix   is not null
UNION
select notename,order_id,total_value, refund_time  from  Y_wish_refunded as ywr LEFT JOIN p_trade_his as ptd on ywr.order_id=ptd.ack where ptd.suffix  is not null
UNION
select notename,order_id,total_value,refund_time   from  Y_wish_refunded as ywr LEFT JOIN p_trade_b as ptd on ywr.order_id=ptd.ack
where  ptd.nid is not null
--select * from #wishOrders where refund_time BETWEEN '2017-02-01' and '2017-02-28'
select suffix,refund_time,sum(total_value)as total_value into #wishRefund from #wishOrders as wos group by suffix, refund_time

--smt 的退款费用
create table #smtOrders (suffix varchar(30) ,ack varchar(50),total_value DECIMAL(10,2), refund_time datetime,currencyCode VARCHAR(6) )--,currencyCode
insert into #smtOrders
select ptd.suffix as notename,order_id,total_value,refund_time,ywr.currencyCode  from  Y_smt_refunded as ywr LEFT JOIN p_trade as ptd on ywr.order_id=ptd.ack where ptd.suffix  is not null
UNION
select ptd.suffix as notename,order_id,total_value,refund_time ,ywr.currencyCode from  Y_smt_refunded as ywr LEFT JOIN p_tradeun as ptd on ywr.order_id=ptd.ack where  ptd.PROTECTIONELIGIBILITYTYPE  not in ('取消订单', '其它异常单') and ptd.suffix   is not null
UNION
select ptd.suffix as notename,order_id,total_value, refund_time,ywr.currencyCode  from  Y_smt_refunded as ywr LEFT JOIN p_trade_his as ptd on ywr.order_id=ptd.ack where ptd.suffix  is not null
UNION
select ptd.suffix as notename,order_id,total_value,refund_time,ywr.currencyCode   from  Y_smt_refunded as ywr LEFT JOIN p_trade_b as ptd on ywr.order_id=ptd.ack
where  ptd.nid is not null

select suffix,refund_time,
CASE WHEN currencyCode='CNY'
THEN sum(total_value)/(SELECT ExchangeRate from B_CurrencyCode where CURRENCYCODE='USD')
ELSE sum(total_value)
END as total_value
--,currencyCode

into #smtRefund
 from #smtOrders as wos group by suffix, refund_time,currencyCode
--ORDER BY currencyCode

-- 合并ebay 和wish 的退款费用导同一张表里面
--2017-03-25 add smt 退款费用

create table #tempsuffixFee(suffix varchar(50), refund DECIMAL(10,2))
--插入eBay退款
insert INTO #tempsuffixFee select suffix ,abs(sum(netValue)) as refund
from #finalRefund
where transactionTime between @BeginDate and @endDate
group by suffix
--插入wish退款
insert INTO #tempsuffixFee select suffix ,sum(total_value) as refund
from #wishRefund
where  refund_time  between @BeginDate and @endDate
group by suffix
--插入smt 退款
INSERT into #tempsuffixFee  SELECT suffix,sum(total_value) as refund
FROM #smtRefund
WHERE refund_time BETWEEN @BeginDate AND @endDate
group by suffix

--死库处理(线下清仓 分摊金额 作为死库费用)

select gpf.*
,isnull(sm.SaleMoneyznrate*sf.refund,0)*@ExchangeRate2	as refund	      --退款金额

,isnull(sm.SaleMoneyznrate*o.diefeeZn,0)	as diefeeZn	                  --死库金额

,isnull(sm.SaleMoneyznrate*e.insertionFee,0) as	insertionFee	          --店铺杂费
,isnull(sm.SaleMoneyznrate*op.saleOpeFeeZn,0)	as saleOpeFeeZn	                  --运营杂费金额

--,sm.SaleMoneyznrate -- 销售额占比
,round((gpf.SaleMoneyzn-gpf.eBayFeeznebay-gpf.ppFeezn-gpf.CostMoney-gpf.ExpressFare-gpf.inpackageMoney
-isnull(sm.SaleMoneyznrate*sf.refund*@ExchangeRate2,0)
-isnull(sm.SaleMoneyznrate*o.diefeeZn,0)
-isnull(sm.SaleMoneyznrate*op.saleOpeFeeZn,0)
-isnull(sm.SaleMoneyznrate*e.insertionFee,0)),2)
 as  grossprofit 	                    	--毛利
,CASE WHEN gpf.SaleMoneyzn=0 THEN 0
ELSE round(((gpf.SaleMoneyzn-gpf.eBayFeeznebay-gpf.ppFeezn-gpf.CostMoney-gpf.ExpressFare-gpf.inpackageMoney
-isnull(sm.SaleMoneyznrate*sf.refund,0)
-isnull(sm.SaleMoneyznrate*o.diefeeZn,0)
-isnull(sm.SaleMoneyznrate*op.saleOpeFeeZn,0)
-isnull(sm.SaleMoneyznrate*e.insertionFee,0))*100/gpf.SaleMoneyzn),2) END
as grossprofitRate	                        --毛利率
into #tmpret
from #grossProfit gpf
LEFT JOIN #tempsuffixFee sf ON sf.suffix=gpf.suffix 																							-- refund fee
LEFT JOIN #ebayInsertionFee e ON e.accountName=gpf.suffix                                         --ebayInsertionFee
LEFT JOIN #tempofflineclearn o ON o.suffix=gpf.suffix                                             -- bad stock fee
LEFT JOIN #tempOpeFee op ON op.suffix=gpf.suffix                                                  --operate fee

LEFT JOIN #SaleMoneyznrate as sm ON sm.suffix=gpf.suffix and sm.storename=gpf.storename           --order by gpt.suffix stl.



-- 新的汇总方法
-- select
-- '汇总' as pingtai,
-- '汇总' as Suffix,
-- '汇总' salesman,
-- SUM(SaleMoney) as SaleMoney,
-- SUM(SaleMoneyzn) as SaleMoneyzn ,
-- SUM(eBayFeeebay) as eBayFeeebay,
-- SUM(eBayFeeznebay) as eBayFeeznebay,
-- SUM(ppFee) as ppFee ,
-- SUM(ppFeezn) as ppFeezn ,
-- SUM(CostMoney) as CostMoney,
-- SUM(ExpressFare) as ExpressFare,
-- SUM(inpackageMoney) as inpackageMoney,
-- '汇总' as StoreName,
-- SUM(refund) as refund,
-- SUM(diefeeZn) as diefeeZn, 					          --死库处理
-- SUM(insertionFee) as insertionFee,        --店铺杂费,e.insertionFee
-- SUM(saleopefeeZn) as saleopefeeZn,          --运营杂费
-- SUM(grossprofit) as grossprofit,
-- CASE WHEN SUM(SaleMoneyzn)=0 THEN 0
-- ELSE
-- round((SUM(grossprofit)/SUM(SaleMoneyzn)*100),2)
-- end as grossprofitRate
-- from  #tmpret
-- UNION
select * from #tmpret







  drop table #TempTradeInfo
  drop table #tbSalerAliasName
  drop table #tbSaler
	drop table #tbStoreName
  drop table #TempTradeInfo_NID
  drop table #TempTradeInfo_TempNID
  drop table #tempskucostprice
	drop table #grossProfit
	drop table #suffixTotal
  DROP table #tmpret
DROP TABLE #tempOpeFee
drop table #tmprefund
drop table #finalRefund
drop table #wishOrders
drop table #wishRefund
end

