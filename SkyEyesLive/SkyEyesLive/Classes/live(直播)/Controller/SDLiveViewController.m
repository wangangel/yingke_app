//
//  SDLiveViewController.m
//  SkyEyesLive
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//

#import "SDLiveViewController.h"
#import "UIView+Extension.h"
#import "UIBarButtonItem+Extension.h"
@implementation SDLiveViewController

- (void)viewDidLoad
{
    [super viewDidLoad];
   
    self.navigationItem.rightBarButtonItem = [UIBarButtonItem itemWithImageName:@"u78" highImageName:@"u78" target:self action:@selector(addtag)];

    self.navigationItem.leftBarButtonItem = [UIBarButtonItem itemWithImageName1:@"u256" highImageName1:@"u256" target1:self action1:@selector(pop) frame1:CGRectMake(0, 0, 100, 44) imageName:@"u193" highImageName:@"u193" target:self action:@selector(pop1)];
    
}

- (void)addtag
{
    NSLog(@"addtag");
}

- (void)pop
{
    NSLog(@"pop");
}

- (void)pop1
{
    NSLog(@"pop1");
}

@end
