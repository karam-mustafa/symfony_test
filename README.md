#Symfony orders Crud Task

#available end points
- [GET] /api/v1/orders
  - return list of orders contains [id, status, delivery_time,  and items for order items]
- [GET] /api/v1/orders/:id
  - show custom order by id
- [PATCH] /api/v1/orders/:id
  - update custom status order by order id
- [POST] /api/v1/orders/
  - request to save a new order record, the request parameters are 
    - id
    - delivery_time
    - delivery_address
    - billing_address
    - customer_id
    - items
      - id
      - quantity
- [DELETE] /api/v1/orders/:id
  - delete custom order
- [GET] /api/v1/delayed-orders
  - get list of delayed orders


#Verify delayed orders
to verify delayed orders please run this command ``symfony console verify:delayed:order``