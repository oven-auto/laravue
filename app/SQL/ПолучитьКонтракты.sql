#Получить все контракты (в данном примере в условии WHERE основного запроса установлено условие что дебиторская задолженость = 0)
select 
	`wsm_reserve_new_car_contracts`.*, 
	`complect_price`.`price` as `cpprice`, 
	`car_fp`.`overprice` as `cfpover`, 
	`car_fp`.`tuningprice` as `cfptuning`, 
	`car_fp`.`giftprice` as `cfpgift`, 
	`_options`.`price` as `optprice`, 
	`_discounts`.`amount` as `dsamount`, 
	`_payments`.`amount` as `payamount`, 
	`_tradins`.`price` as `usedprice`, (
            IFNULL(complect_price.price,0) + 
            IFNULL(_options.price, 0) + 
            IFNULL(car_fp.overprice,0) + 
            IFNULL(car_fp.tuningprice, 0) - 
            IFNULL(car_fp.giftprice, 0) - 
            IFNULL(_discounts.amount, 0) - 
            IFNULL(_payments.amount, 0) - 
            IFNULL(_tradins.price, 0)
	) as d
    from `wsm_reserve_new_car_contracts` 
    left join `wsm_reserve_new_cars` on `wsm_reserve_new_cars`.`id` = `wsm_reserve_new_car_contracts`.`reserve_id` 
    left join `cars` on `cars`.`id` = `wsm_reserve_new_cars`.`car_id` 
    left join `worksheets` on `worksheets`.`id` = `wsm_reserve_new_cars`.`worksheet_id` 
    left join `clients` on `clients`.`id` = `worksheets`.`client_id` 
    left join `wsm_reserve_sales` on `wsm_reserve_sales`.`reserve_id` = `wsm_reserve_new_cars`.`id` 
    left join `car_orders` on `car_orders`.`car_id` = `cars`.`id` 
    left join `wsm_reserve_complectation_prices` as `contract_cp` on `contract_cp`.`contract_id` = `wsm_reserve_new_car_contracts`.`id` 
    left join `complectation_prices` as `complect_price` on `complect_price`.`id` = `contract_cp`.`complectation_price_id` 
    left join (
    	SELECT 
    		sum(opt_price.price) as price, 
    		contract_op.contract_id 
    	FROM wsm_reserve_option_prices as contract_op
		LEFT JOIN option_prices as opt_price on opt_price.id = contract_op.option_price_id
		GROUP BY contract_op.contract_id
	) as _options on `_options`.`contract_id` = `wsm_reserve_new_car_contracts`.`id` 
	left join `car_full_prices` as `car_fp` on `car_fp`.`car_id` = `cars`.`id` 
	left join (
		SELECT 
			discounts.modulable_id as reserve_id, 
			sum(discount_sums.amount) as amount 
		FROM discounts
		LEFT JOIN discount_sums on discount_sums.discount_id = discounts.id
		WHERE discounts.modulable_type = "App\\Models\\WsmReserveNewCar"
		GROUP BY discounts.modulable_id
	) as _discounts on `_discounts`.`reserve_id` = `wsm_reserve_new_car_contracts`.`reserve_id` 
	left join (
		SELECT 
			wrt.reserve_id as reserve_id, 
			sum(used_cars.purchase_price) as price 
		FROM wsm_reserve_trade_ins as wrt
		LEFT JOIN used_cars on used_cars.id = wrt.used_car_id
		GROUP BY wrt.reserve_id
	) as _tradins on `_tradins`.`reserve_id` = `wsm_reserve_new_car_contracts`.`reserve_id` 
	left join (
		SELECT 
			wrp.reserve_id as reserve_id, 
			sum(wrp.amount) as amount 
		FROM wsm_reserve_payments as wrp 
		GROUP BY wrp.reserve_id
	) as _payments on `_payments`.`reserve_id` = `wsm_reserve_new_car_contracts`.`reserve_id` 
	where (
            IFNULL(complect_price.price,0) + 
            IFNULL(_options.price, 0) + 
            IFNULL(car_fp.overprice,0) + 
            IFNULL(car_fp.tuningprice, 0) - 
            IFNULL(car_fp.giftprice, 0) - 
            IFNULL(_discounts.amount, 0) - 
            IFNULL(_payments.amount, 0) - 
            IFNULL(_tradins.price, 0)) = 0
	group by `wsm_reserve_new_car_contracts`.`id` 
	order by `wsm_reserve_new_car_contracts`.`id` desc