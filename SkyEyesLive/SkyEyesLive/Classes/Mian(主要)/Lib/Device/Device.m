//
//  Device.m
//  test2
//
//  Created by 孙达 on 15/6/30.
//  Copyright (c) 2015年 OTT6IOS. All rights reserved.
//

#import "Device.h"

@implementation Device

+ (CGSize)deviceSize
{
    return [UIScreen mainScreen].bounds.size;
}
#define UIDESIGNWIDTH  1242.0
#define UIDESIGNHEIGHT 2208.0

+ (CGRect)X:(CGFloat)X Y:(CGFloat)Y width:(CGFloat)width height:(CGFloat)height;
{
    CGSize screenSize = [Device deviceSize];
    
    CGFloat fScaleX = UIDESIGNWIDTH/screenSize.width;
    CGFloat fScaleY = UIDESIGNHEIGHT/screenSize.height;
    
    CGFloat fPosX = 0, fPosY = 0, fWidth = 0, fHeight = 0;
    
    fPosX = X/fScaleX;
    fPosY = Y/fScaleY;
    fWidth = width/fScaleX;
    fHeight = height/fScaleY;
    return CGRectMake(X/fScaleX, Y/fScaleY, width/fScaleX, height/fScaleY);
}

+ (CGRect)x:(CGFloat)myX y:(CGFloat)myY width:(CGFloat)w height:(CGFloat)h
{
    //    if ([UIDevice currentDevice].userInterfaceIdiom==UIUserInterfaceIdiomPad)
    //    {
    //        return CGRectMake([UIScreen mainScreen].bounds.size.width/1024*myX, [UIScreen mainScreen].bounds.size.height/768*myY, [UIScreen mainScreen].bounds.size.width/1024*w, [UIScreen mainScreen].bounds.size.height/768*h);
    //    }
    return CGRectMake([UIScreen mainScreen].bounds.size.width/568*myX, [UIScreen mainScreen].bounds.size.height/320*myY, [UIScreen mainScreen].bounds.size.width/568*w, [UIScreen mainScreen].bounds.size.height/320*h);
}

+ (CGPoint)X:(CGFloat)X Y:(CGFloat)Y
{
    CGSize screenSize = [Device deviceSize];
    
    CGFloat fScaleX = UIDESIGNWIDTH/screenSize.width;
    CGFloat fScaleY = UIDESIGNHEIGHT/screenSize.height;
    
    return CGPointMake(X/fScaleX, Y/fScaleY);
}

+ (CGPoint)getViewScale
{
    CGSize screenSize = [Device deviceSize];
    CGFloat fScaleX = UIDESIGNWIDTH/screenSize.width;
    CGFloat fScaleY = UIDESIGNHEIGHT/screenSize.height;
    return CGPointMake(fScaleX, fScaleY);
}

+ (BOOL)isPhone
{
    if (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPhone)
    {
        return YES;
    }
    return NO;
}

+ (CGSize)width:(CGFloat)width height:(CGFloat)height
{
    
    CGSize screenSize = [Device deviceSize];
    CGFloat fScaleX = UIDESIGNWIDTH/screenSize.width;
    CGFloat fScaleY = UIDESIGNHEIGHT/screenSize.height;
    return CGSizeMake( width/fScaleX, height/fScaleY);
}


@end
