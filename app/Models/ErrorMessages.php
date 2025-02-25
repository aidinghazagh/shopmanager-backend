<?php

namespace App\Models;

class ErrorMessages
{
    private  static string $defaultLang = 'en';

    // Using a map to save error messages
    private static array $messages = [
        'en' => [
            'shop_phone_not_found' => "No shop found with phone number %s",
            'password_not_match' => "Password does not match",
            'string' => "%s is not a valid string",
            'integer' => "%s is not a valid integer",
            'required' => "%s is required",
            'unauthenticated' => "Please provide a valid token",
            'shop_expired' => "Please renew your shop subscription",
            'max' => "Max characters for %s is %s characters",
            'min' => "Min characters for %s is %s characters",
            'invalid_language' => "Invalid language %s",
            'unauthorized' => "Unauthorized request",
            'locked_order' => "This order has payments and cannot be edited.",
            'array' => "%s is not a valid array",
            'order_for_shop_not_found' => "Order for this shop not found",
            'order_id_not_changeable' => "Cannot change dedicated order",
            'product_not_found' => "Product: %s not found",
        ],
        'fa' => [
            'shop_phone_not_found' => "فروشگاهی با این شماره تلفن پیدا نشد: %s",
            'password_not_match' => "رمز عبور نادرست است",
            'string' => "%s "."باید یک رشته کارکتری باشد",
            'integer' => "%s "."باید یک عدد صحیح باشد",
            'required' => "%s "."الزامی است",
            'unauthenticated' => "لطفا یک توکن معتبر ارسال کنید",
            'shop_expired' => "اشتراک فروشگاه خود را تمدید کنید",
            'max' => "حداکثر کارکتر برای %s %s است",
            'min' => "حداقل کارکتر برای %s %s است",
            'invalid_language' => "اشتباه است"." %s "."زبان وارد شده",
            'unauthorized' => "درخواست غیر مجاز",
            'locked_order' => "این سفارش دارای پرداختی میباشد و قابل ویرایش نیست",
            'array' => "%s "."باید یک آرایه باشد",
            'order_for_shop_not_found' => "این سفارش برای فروشگاه پیدا نشد",
            'order_id_not_changeable' => "نمی توان سفارش انتخاب شده را تغییر داد",
            'product_not_found' => "پیدا نشد"." %s "."محصول ",
        ],
    ];
    public static function isLanguageValid(string $lang): bool
    {
        return array_key_exists($lang, self::$messages);
    }
    private static array $translations = [
        'fa' => [
            'phone' => "شماره تلفن",
            'password' => "رمز عبور",
            'name' => "نام",
            'language' => "زبان",
            'price' => "قیمت",
            'purchase_price' => "قیمت خرید",
            'inventory' => "موجودی",
            'customer' => "مشتری",
            'discount' => "تخفیف",
            'products' => "محصولات",
            'paid' => "پرداختی",
            'products.*' => "محتوای محصولات",
            'order_id' => "آیدی سفارش",
            'amount' => "مقدار",
        ],
    ];

    public static function getMessage(?string $language, string $key, string ...$args): string
    {
        // Use the default language if none is provided
        $language = $language ?? self::$defaultLang;

        if (isset(self::$messages[$language][$key])) {
            $message = self::$messages[$language][$key];
            // If arguments are provided, format the message
            return $args ? sprintf($message, ...$args) : $message;
        }

        return "Message not found for language $language.";
    }
    public static function getTranslation(?string $language, string $key): string
    {
        // If no language provided or the language is english just return itself
        if ($language == null | $language == 'en') {
            return $key;
        }
        if (isset(self::$translations[$language][$key])) {
            return self::$translations[$language][$key];
        }
        return "Translation not available.";
    }
}

