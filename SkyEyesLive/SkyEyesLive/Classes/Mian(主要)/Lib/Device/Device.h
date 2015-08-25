//
//  Device.h
//  test2
//
//  Created by 孙达 on 15/6/30.
//  Copyright (c) 2015年 OTT6IOS. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <UIKit/UIKit.h>
@interface Device : NSObject
+ (CGSize)deviceSize;
/**
 *  按照设计分辨率 进行适配
 *
 *  @param X      X值
 *  @param Y      Y值
 *  @param width  width值
 *  @param height height值
 *
 *  @return 返回一个CGRectMake
 */
+ (CGRect)X:(CGFloat)X Y:(CGFloat)Y width:(CGFloat)width height:(CGFloat)height;


/**
 *  根据设计分辨率设置控件的位置
 *
 *  @param X 设计分辨率X值
 *  @param Y 设计分辨率Y值
 *
 *  @return 设备中的点坐标
 */
+ (CGPoint)X:(CGFloat)X Y:(CGFloat)Y;

/**
 *  获取UI缩放值
 *
 *  @return 返回CGPoint .x为X方向缩放，.y为Y方向缩放。
 */
+ (CGPoint)getViewScale;

/**
 *  判断是设备是iPhone还是iPad
 *
 *  @return BOOl YES为iPhone
 */
+ (BOOL)isPhone;


+ (CGSize)width:(CGFloat)width height:(CGFloat)height;

@end
