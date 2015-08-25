//
//  SDTabBarViewController.m
//  SkyEyesLive
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//

#import "SDTabBarViewController.h"
#import "SDLiveViewController.h"
#import "SDRanKingTableViewController.h"
#import "SDSquareTableViewController.h"

@implementation SDTabBarViewController

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    // 添加子控制器
    
    SDSquareTableViewController *home = [[SDSquareTableViewController alloc] init];
    [self addOneChlildVc:home title:@"广场" imageName:@"tabbar_home" selectedImageName:@"tabbar_home_selected"];
    
    SDLiveViewController *message = [[SDLiveViewController alloc] init];
    [self addOneChlildVc:message title:@"我要直播" imageName:@"tabbar_message_center" selectedImageName:@"tabbar_message_center_selected"];
    
    SDSquareTableViewController *discover = [[SDSquareTableViewController alloc] init];
    [self addOneChlildVc:discover title:@"排行" imageName:@"tabbar_discover" selectedImageName:@"tabbar_discover_selected"];
    
   
}


/**
 *  添加一个子控制器
 *
 *  @param childVc           子控制器对象
 *  @param title             标题
 *  @param imageName         图标
 *  @param selectedImageName 选中的图标
 */
- (void)addOneChlildVc:(UIViewController *)childVc title:(NSString *)title imageName:(NSString *)imageName selectedImageName:(NSString *)selectedImageName
{
   
    childVc.title = title;
//    childVc.tabBarItem.title = title;
//    childVc.navigationItem.title = title;
//    

    childVc.tabBarItem.image = [UIImage imageNamed:imageName];
    
  
    UIImage *selectedImage = [UIImage imageNamed:selectedImageName];
    childVc.tabBarItem.selectedImage = selectedImage;
    
    // 添加为tabbar控制器的子控制器
    UINavigationController *nav = [[UINavigationController alloc] initWithRootViewController:childVc];
    [self addChildViewController:nav];
}

@end
