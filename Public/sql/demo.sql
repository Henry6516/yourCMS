ALTER PROCEDURE [dbo].[P_test_oaGoods_TowishGoods]
@pid INT
AS
BEGIN
 Set XACT_ABORT ON;
--插入wishgoods模板
--SKU 单属性多属性
--区分单属性还是多属性
DECLARE @skuNum int;
SET @skuNum = (SELECT COUNT(*)FROM oa_goodssku WHERE pid=@pid)
DECLARE @price DECIMAL(6,2)
set @price=(select salePrice FROM oa_goods as ogs LEFT JOIN oa_goodsinfo as ofo on ogs.nid=ofo.goodsid  WHERE ofo.pid=@pid)
DECLARE @haveBeen INT
set @haveBeen=(select count(*) from oa_wishgoods where infoid=@pid)

--oa_goodssku 增加新记录 记录后插入oa_wishgoodssku
SELECT t1.sid
into #toCreate
FROM oa_goodssku t1 LEFT JOIN oa_wishgoodssku  t2 ON t1.sid=t2.sid WHERE  t2.sid is null  and t1.pid=@pid


Begin Tran toWish
IF @haveBeen=0

	BEGIN
		IF @skuNum > 1 --多属性
			BEGIN
				INSERT into oa_wishgoods
				( SKU,title,description,inventory,price,msrp,shipping,shippingtime,tags,main_image,goodsid,infoid,extra_images,
				headKeywords,requiredKeywords,randomKeywords,tailKeywords,wishtags)
					SELECT
					oa_goodsinfo.GoodsCode as SKU,
					'' as title,
					oa_goodsinfo.description as description,  --oa_goodsinfo.description
					10000 AS inventory,
					oa_goods.salePrice AS price,
					CEILING(oa_goods.salePrice*6) as msrp,
					'0' as shipping,
					'7-21' as shippingtime,
					'' as tags,
					'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_0_.jpg' as main_image,
					oa_goodsinfo.bgoodsid  as goodsid,
					oa_goodsinfo.pid as infoid,
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_00_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_1_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_2_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_3_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_4_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_5_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_6_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_7_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_8_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_9_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_10_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_11_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_12_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_13_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_14_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_15_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_16_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_17_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_18_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_19_.jpg'+char(10) as extra_images,
				headKeywords,requiredKeywords,randomKeywords,tailKeywords,wishtags
					FROM oa_goodsinfo
					 LEFT JOIN oa_goods ON oa_goodsinfo.goodsid=oa_goods.nid
					WHERE oa_goodsinfo.pid = @pid

			END

		ELSE      --单属性情况
			BEGIN
				INSERT into oa_wishgoods
				( SKU,title,description,inventory,price,msrp,shipping,shippingtime,tags,main_image,goodsid,infoid,extra_images,headKeywords,requiredKeywords,randomKeywords,tailKeywords,wishtags)
					SELECT
					oa_goodsinfo.GoodsCode+'01' as SKU,
					'' as title,
					oa_goodsinfo.description as description,  --oa_goodsinfo.description
					10000 AS inventory,
					oa_goods.salePrice AS price,
					CEILING(oa_goods.salePrice*6) as msrp,
					'0' as shipping,
					'7-21' as shippingtime,
					'' as tags,
					'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_0_.jpg' as main_image,
					oa_goodsinfo.bgoodsid  as goodsid,
					oa_goodsinfo.pid as infoid,
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_1_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_2_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_3_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_4_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_5_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_6_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_7_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_8_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_9_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_10_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_11_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_12_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_13_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_14_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_15_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_16_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_17_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_18_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_19_.jpg'+char(10)+
				'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_20_.jpg' as extra_images ,
				headKeywords,requiredKeywords,randomKeywords,tailKeywords,wishtags
					FROM oa_goodsinfo
					 LEFT JOIN oa_goods ON oa_goodsinfo.goodsid=oa_goods.nid
					WHERE oa_goodsinfo.pid = @pid
				END


			--插入wishsku 模板
			INSERT INTO oa_wishgoodssku (
			pid,
			sid,
			sku,
			color,
			[size],
			inventory,
			price,
			shipping,
			msrp,
			shipping_time,
			linkurl,
			goodsskuid,
			Weight
			)

			SELECT pid,
						sid,
						sku,
						property1 as color,
						property2 as [size],
			10000 as inventory,
			RetailPrice as price,
			0 as shipping,
			ceiling(@price*6) as msrp,
			'7-21' as shipping_time,
			linkurl,
			goodsskuid,
			Weight
			FROM oa_goodssku
			where pid=@pid


	END


IF @haveBeen >= 1


	BEGIN

		update oa_wishgoods SET
		SKU = B_Goods.sku,
		description = oa_goodsinfo.description,
		price = oa_goods.salePrice,
		msrp = CEILING(oa_goods.salePrice*6),
		headKeywords = oa_goodsinfo.headKeywords,
		requiredKeywords = oa_goodsinfo.requiredKeywords,
		randomKeywords=oa_goodsinfo.randomKeywords,
		tailKeywords= oa_goodsinfo.tailKeywords,
		wishtags = oa_goodsinfo.wishtags,
		main_image ='https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_0_.jpg',
		extra_images = 'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_00_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_1_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_2_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_3_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_4_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_5_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_6_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_7_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_8_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_9_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_10_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_11_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_12_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_13_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_14_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_15_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_16_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_17_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_18_.jpg'+char(10)+
		'https://www.tupianku.com/view/full/10023/'+oa_goodsinfo.GoodsCode+'-_19_.jpg'+char(10)
		FROM oa_wishgoods
		LEFT JOIN oa_goodsinfo on oa_wishgoods.infoid = oa_goodsinfo.pid
		LEFT JOIN oa_goods ON oa_goodsinfo.goodsid=oa_goods.nid
		LEFT JOIN b_goods on B_Goods.nid = oa_goodsinfo.bgoodsid
		WHERE oa_goodsinfo.pid = @pid

		--更新子SKU 根据oa_goodssku
		----UPDATE t1 SET t1.linkurl=t2.linkurl FROM oa_wishgoodssku t1 LEFT JOIN oa_goodssku t2 ON t1.pid=t2.pid  WHERE t1.linkurl is null  AND t1.SKU LIKE '%8J0047%'
		UPDATE  ows SET
		ows.pid=ogs.pid,
		ows.sid=ogs.sid,
		ows.sku=ogs.sku,
		ows.color=ogs.property1,
		ows.[size]=ogs.property2,
		ows.inventory=10000,
		ows.price=ogs.RetailPrice,
		ows.shipping=0,
		ows.msrp = ceiling(@price*6),
		ows.shipping_time = '7-21',
		ows.linkurl=ogs.linkurl,
		ows.goodsskuid=ogs.goodsskuid,
		ows.Weight=ogs.Weight
		FROM oa_wishgoodssku ows
		LEFT JOIN oa_goodssku ogs ON ogs.sku=ows.sku
		WHERE ogs.pid = @pid

		--不存在就创建

		INSERT INTO oa_wishgoodssku (	pid,sid,sku,color,[size],inventory,	price,shipping,	msrp,shipping_time,linkurl,goodsskuid,Weight)

			SELECT pid,
						sid,
						sku,
						property1 as color,
						property2 as [size],
			10000 as inventory,
			RetailPrice as price,
			0 as shipping,
			ceiling(@price*6) as msrp,
			'7-21' as shipping_time,
			linkurl,
			goodsskuid,
			Weight
			FROM oa_goodssku
			where sid IN (SELECT * FROM #toCreate)


 END


--james 2017-12-29 子SKU暂不考虑



if @@error<>0
		BEGIN
			ROLLBACK TRAN toWish
		END
ELSE
		COMMIT TRAN toWish

drop TABLE #toCreate;


END