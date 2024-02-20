# PHP-E-Commerce-Capstone
## To-do:
  - Add products for admin with images
  
## Timestamps:
Day 1: 1h27m
  - Planning and creation of database & ERD

Day 2: 4h40m
  - Upload of clickable prototype, and update of ERD to remove currency, since bank payments usually do the currency conversion anyway. (1h40m)
  - Finished (3h) :
    - Created model for csrf and xss security
    - Login and signup validation
    - Password encryption
    - Obtaining and adding records from database
    - Display of errors
    - Fixed the label sliding animation in the template of the login and signup forms to work as intended

Day 3: ~7h38m
  - Retain old form values except for passwords when errors in the form values are detected. Primary user functionality is now complete. (5m)
  - Updated ERD to include user image, and added a default image. (3m)
  - Added comments to the Users controller, User model, and Defence model.
  - Added showing the user image and a clickable dropdown menu to logout for the user dashboard and admin dashboard (1h30m)
  - Time: ~6h:
    - Adjustment for the admin product page, so long product names can show on hover, and not move below the image. (This took around 2-3h)
    - Populated the product_types table
    - Added missing icons for the different meat categories
    - Linked categories to the product_types table
    - Linked the products table to the admin products dashboard
    - Created and tested the validation for the add product form, including the upload of multiple images, and deletion of uploaded images if errors are detected.
Day 4: 7h42m
  - Use of JS to show preview of images to upload, and selection of main image. (55m)
  - Made client side validation for images to upload. (1h30m)
  - Finished add product. (2h25m)
  - Fixed admin product view. (46m)
  - Edit product 90% complete (2h6m)

## ERD:

*Note: user_billings, user_shippings, and user_cards are encrypted, as well as the password field in the users table*
![image](https://github.com/JuddKarloCarreon/PHP-E-Commerce-Capstone/assets/156634638/7d60a6a5-ed88-4c6a-b165-ffd5480c2366)

