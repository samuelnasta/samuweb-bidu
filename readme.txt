=== Samuweb Bidu ===
Contributors: Samuweb
Tags:
Requires at least: 4.4.2
Tested up to: 4.4.2

Samuweb Bidu is a bidding system

== Description ==

You can setup the auctions, auctioneers and users.
Lets you view and edit the bids too.



== Installation ==

1. ---Go to your post. You'll notice a new icon.
2. Select the text you want to be the answer to your question and click the icon.
3. Put the <strong>Samuweb Related Questions</strong> box wherever you want in your theme.
If you're lazy like me, just copy and paste from here:</p>
`<div id="samuweb-related-questions-box"></div>`
4.That's all, folks! You're ready to rock!
And the best thing: it's so light that it doesn't even need jQuery



== Screenshots ==

1. ---The new icon on your text editor
2. Samuweb Related Questions in action
3. Voilà! A box of questions automatically generated!





//      wp-user-manager/includes/forms/class-wpum-form-register.php
//
//        public static function do_registration()
//            if( array_key_exists( 'address' , $values['register'] ) )
//                update_user_meta( $user_id, 'address', $values['register']['address'] );
//            if( array_key_exists( 'city' , $values['register'] ) )
//                update_user_meta( $user_id, 'city', $values['register']['city'] );
//            if( array_key_exists( 'province' , $values['register'] ) )
//                update_user_meta( $user_id, 'province', $values['register']['province'] );
//            if( array_key_exists( 'phone' , $values['register'] ) )
//                update_user_meta( $user_id, 'phone', $values['register']['phone'] );
//            if( array_key_exists( 'cpf' , $values['register'] ) )
//                update_user_meta( $user_id, 'cpf', $values['register']['cpf'] );

//       public static function function update_profile
//            if( array_key_exists( 'address' , $user_data ) )
//                update_user_meta( self::$user->ID, 'address', $user_data['address'] );
//            if( array_key_exists( 'city' , $user_data ) )
//                update_user_meta( self::$user->ID, 'city', $user_data['city'] );
//            if( array_key_exists( 'province' , $user_data ) )
//                update_user_meta( self::$user->ID, 'province', $user_data['province'] );
//            if( array_key_exists( 'phone' , $user_data ) )
//                update_user_meta( self::$user->ID, 'phone', $user_data['phone'] );
//            if( array_key_exists( 'cpf' , $user_data ) )
//                update_user_meta( self::$user->ID, 'cpf', $user_data['cpf'] );
