//
//  UIBarButtonItem+Extension.h
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//


#import <UIKit/UIKit.h>

@interface UIBarButtonItem (Extension)
+ (UIBarButtonItem *)itemWithImageName:(NSString *)imageName highImageName:(NSString *)highImageName target:(id)target action:(SEL)action;

+ (UIBarButtonItem *)itemWithImageName1:(NSString *)imageName1 highImageName1:(NSString *)highImageName1 target1:(id)target1 action1:(SEL)action1 frame1:(CGRect)frame1  imageName:(NSString *)imageName highImageName:(NSString *)highImageName target:(id)target action:(SEL)action;
@end
