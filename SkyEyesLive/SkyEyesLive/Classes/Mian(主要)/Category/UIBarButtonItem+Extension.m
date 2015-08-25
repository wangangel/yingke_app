//
//  UIBarButtonItem+Extension.m
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//

#import "UIBarButtonItem+Extension.h"
#import "UIView+Extension.h"

@implementation UIBarButtonItem (Extension)

/**
 *  通过2张图片创建buttom
 *
 *  @param imageName     默认图片
 *  @param highImageName 点击图片
 *  @param target        哪个掉的
 *  @param action        方法
 *
 *  @return <#return value description#>
 */

+ (UIBarButtonItem *)itemWithImageName:(NSString *)imageName highImageName:(NSString *)highImageName target:(id)target action:(SEL)action
{
    UIButton *button = [[UIButton alloc] init];
    [button setBackgroundImage:[UIImage imageNamed:imageName] forState:UIControlStateNormal];
    [button setBackgroundImage:[UIImage imageNamed:highImageName] forState:UIControlStateHighlighted];
    
    // 设置按钮的尺寸为背景图片的尺寸
    button.size = button.currentBackgroundImage.size;
    
    // 监听按钮点击
    [button addTarget:target action:action forControlEvents:UIControlEventTouchUpInside];
    return [[UIBarButtonItem alloc] initWithCustomView:button];
}


+ (UIBarButtonItem *)itemWithImageName1:(NSString *)imageName1 highImageName1:(NSString *)highImageName1 target1:(id)target1 action1:(SEL)action1 frame1:(CGRect)frame1  imageName:(NSString *)imageName highImageName:(NSString *)highImageName target:(id)target action:(SEL)action
{
    
    //设置右边按钮
    UIView *leftView = [[UIView alloc] initWithFrame:frame1];
    
    UIButton *leftButton = [[UIButton alloc] init];
    [leftButton setBackgroundImage:[UIImage imageNamed:imageName1] forState:UIControlStateNormal];
    [leftButton setBackgroundImage:[UIImage imageNamed:highImageName1] forState:UIControlStateHighlighted];
    // 设置按钮的尺寸为背景图片的尺寸
    leftButton.size = leftButton.currentBackgroundImage.size;
    // 监听按钮点击
    [leftButton addTarget:target1 action:action1 forControlEvents:UIControlEventTouchUpInside];
    //添加到view上
    [leftView addSubview:leftButton];
    

    UIButton *leftIconButton = [[UIButton alloc] init];
    [leftIconButton setBackgroundImage:[UIImage imageNamed:imageName] forState:UIControlStateNormal];
    [leftIconButton setBackgroundImage:[UIImage imageNamed:highImageName] forState:UIControlStateNormal];
    // 设置按钮的尺寸为背景图片的尺寸
    leftIconButton.frame = CGRectMake(leftButton.frame.size.width, 0, leftIconButton.currentBackgroundImage.size.width, leftIconButton.currentBackgroundImage.size.height);
    // 监听按钮点击
    [leftIconButton addTarget:target action:action forControlEvents:UIControlEventTouchUpInside];

    [leftView addSubview:leftIconButton];
    

    return [[UIBarButtonItem alloc] initWithCustomView:leftView];
}


@end
