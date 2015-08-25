//
//  AppDelegate.m
//  SkyEyesLive
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//

#import "AppDelegate.h"
#import "SDLoginViewController.h"
#import "OTT6PublicHeader.h"
#import "Device.h"
#import "IQKeyboardManager.h"
@interface AppDelegate ()

@end

@implementation AppDelegate


- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {
    //创建窗口
    self.window = [[UIWindow alloc] init];
    self.window.frame = [UIScreen mainScreen].bounds;
    
    //设置窗口的根控制器


    SDLoginViewController *login = [[SDLoginViewController alloc] init];
    
    //login.navigationItem.title = @"登入";

    UINavigationController *nav = [[UINavigationController alloc] initWithRootViewController:login];
    self.window.rootViewController = nav;
    //显示窗口（成为主窗口）
    [self.window makeKeyAndVisible];
    
   
    
    IQKeyboardManager *manager = [IQKeyboardManager sharedManager];
    manager.enable = YES;
    manager.shouldResignOnTouchOutside = YES;
    manager.enableAutoToolbar = NO;
    return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application {
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application {
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application {
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application {
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application {
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

/** 修改状态栏 */
- (UIStatusBarStyle)preferredStatusBarStyle
{
    // 修改状态栏的颜色(白色)
    return UIStatusBarStyleLightContent;
}

@end
