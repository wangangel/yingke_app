//
//  SDLoginViewController.m
//  SkyEyesLive
//
//  Created by sunda on 15/8/24.
//  Copyright (c) 2015年 sunda. All rights reserved.
//

#import "SDLoginViewController.h"
#import "HyperlinksButton.h"
#import "MBProgressHUD+MJ.h"
#import "SDTabBarViewController.h"
#import "Device.h"
#import "OTT6PublicHeader.h"
#import "UIView+Extension.h"


@interface SDLoginViewController ()<UITextFieldDelegate>
@property (nonatomic,strong) UITextField *phoneField;
@property (nonatomic,strong) UITextField *pwdField;
@end


@implementation SDLoginViewController
- (void)viewDidLoad
{
    [super viewDidLoad];
    self.view.backgroundColor = [UIColor whiteColor];
    self.navigationItem.title = @"登入";
    UIColor * color = SDColor(255, 255, 255);
    NSDictionary * dict = [NSDictionary dictionaryWithObject:color forKey:NSForegroundColorAttributeName];
    self.navigationController.navigationBar.titleTextAttributes = dict;
    self.navigationController.navigationBar.barTintColor = SDColor(57, 187, 255);
    self.navigationController.navigationBar.barStyle = UIBarStyleBlack;
    [self clearPhone];
    [self clearPwd];
    [self clearLoginUI];
}


- (void)clearLoginUI
{
    UIButton *loginBtn =  [[UIButton alloc] initWithFrame:SDFrame(121, 985, 1000, 136)];
    [loginBtn setBackgroundImage:[UIImage imageNamed:@"lg_btn_bg1_1"] forState:UIControlStateNormal];
    [loginBtn setBackgroundImage:[UIImage imageNamed:@"lg_btn_bg1_2"] forState:UIControlStateHighlighted];
    [loginBtn setTitle:@"登入" forState:UIControlStateNormal];
    [loginBtn setTitleColor:SDColor(57, 187, 255) forState:UIControlStateNormal];
    [loginBtn setTitle:@"登入" forState:UIControlStateHighlighted];
    [loginBtn setTitleColor:SDColor(255, 255, 255) forState:UIControlStateHighlighted];
    [loginBtn addTarget:self action:@selector(LoginClick) forControlEvents:UIControlEventTouchUpInside];
    [self.view addSubview:loginBtn];
    UIButton *registeredBtn = [[UIButton alloc] initWithFrame:SDFrame(121, 1194, 1000, 136)];
    [registeredBtn setBackgroundImage:[UIImage imageNamed:@"lg_btn_bg1_1"] forState:UIControlStateNormal];
    [registeredBtn setBackgroundImage:[UIImage imageNamed:@"lg_btn_bg1_2"] forState:UIControlStateHighlighted];
    [registeredBtn setTitle:@"用户注册" forState:UIControlStateNormal];
    [registeredBtn setTitleColor:SDColor(57, 187, 255) forState:UIControlStateNormal];
    [registeredBtn setTitle:@"用户注册" forState:UIControlStateHighlighted];
    [registeredBtn setTitleColor:SDColor(255, 255, 255) forState:UIControlStateHighlighted];
    [self.view addSubview:registeredBtn];
    UIImageView *otherLoginImageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"sanfang_02"]];
    otherLoginImageView.frame = SDFrame(0, 1582, 1242, 42);
    [self.view addSubview:otherLoginImageView];
    UIButton *sinaWeiboLogin = [[UIButton alloc] initWithFrame:SDFrame(306, 1747, 230, 230)];
    [sinaWeiboLogin setBackgroundImage:[UIImage imageNamed:@"third_sina"] forState:UIControlStateNormal];
    sinaWeiboLogin.adjustsImageWhenHighlighted = NO;
    [self.view addSubview:sinaWeiboLogin];
    UIButton *weChatLogin = [[UIButton alloc] initWithFrame:SDFrame(706, 1747, 230, 230)];
    [weChatLogin setBackgroundImage:[UIImage imageNamed:@"third_wechat"] forState:UIControlStateNormal];
    weChatLogin.adjustsImageWhenHighlighted = NO;
    [self.view addSubview:weChatLogin];
}

- (void)clearPwd
{
    UIView *PwdView = [[UIView alloc] initWithFrame:SDFrame(121, 526, 1000, 170)];
    [self.view addSubview:PwdView];
    CGFloat PwdImageViewX = 70 / SDScaleX;
    CGFloat PwdImageViewW = 64 / SDScaleX;
    CGFloat PwdImageViewH = 72 / SDScaleY;
    CGFloat PwdImageViewY = (PwdView.height - PwdImageViewH) / 2;
    UIImageView *PwdImageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"lg_ico_passowrd"]];
    PwdImageView.frame = CGRectMake(PwdImageViewX, PwdImageViewY, PwdImageViewW, PwdImageViewH);
    [PwdView addSubview:PwdImageView];
    CGFloat PwdFiledX = 212 / SDScaleX;
    CGFloat PwdFiledW = (PwdView.width - PwdFiledX);
    CGFloat PwdFiledH = 72 / SDScaleY;
    CGFloat PwdFiledY = (PwdView.height - PwdFiledH) / 2;
    self.pwdField = [[UITextField alloc] initWithFrame:CGRectMake(PwdFiledX, PwdFiledY, PwdFiledW, PwdFiledH)];
    self.pwdField.placeholder = @"输入密码";
    self.pwdField.keyboardType = UIKeyboardTypeDefault;
    self.pwdField.secureTextEntry = YES;
    self.pwdField.delegate = self;
    [PwdView addSubview:self.pwdField ];
    UIView *dividingLineView = [[UIView alloc] initWithFrame:CGRectMake(0, PwdView.height, PwdView.width, 1)];
    dividingLineView.backgroundColor = SDColor(57, 187, 255);
    [PwdView addSubview:dividingLineView];
    
    UIButton *forgetPwdBtn = [[UIButton alloc] initWithFrame:SDFrame(121, 750, 1000, 100)];
    [forgetPwdBtn setTitle:@"忘记密码?" forState:UIControlStateNormal];
    [forgetPwdBtn setTitleColor:SDColor(57, 187, 255) forState:UIControlStateNormal];
    [forgetPwdBtn setContentHorizontalAlignment:UIControlContentHorizontalAlignmentRight];
    [self.view addSubview:forgetPwdBtn];

    
}
- (void)clearPhone
{
    UIView *phoneView = [[UIView alloc] initWithFrame:SDFrame(121, 355, 1000, 170)];
    [self.view addSubview:phoneView];
    CGFloat phoneImageViewX = 70 / SDScaleX;
    CGFloat phoneImageViewW = 64 / SDScaleX;
    CGFloat phoneImageViewH = 72 / SDScaleY;
    CGFloat phoneImageViewY = (phoneView.height - phoneImageViewH) / 2;
    UIImageView *phoneImageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"lg_ico_user"]];
    phoneImageView.frame = CGRectMake(phoneImageViewX, phoneImageViewY, phoneImageViewW, phoneImageViewH);
    [phoneView addSubview:phoneImageView];
    CGFloat phoneFiledX = 212 / SDScaleX;
    CGFloat phoneFiledW = (phoneView.width - phoneFiledX);
    CGFloat phoneFiledH = 72 / SDScaleY;
    CGFloat phoneFiledY = (phoneView.height - phoneFiledH) / 2;
    self.phoneField = [[UITextField alloc] initWithFrame:CGRectMake(phoneFiledX, phoneFiledY, phoneFiledW, phoneFiledH)];
    self.phoneField.placeholder = @"输入手机号";
    self.phoneField.keyboardType = UIKeyboardTypeNumberPad;
    [phoneView addSubview:self.phoneField];
    UIView *dividingLineView = [[UIView alloc] initWithFrame:CGRectMake(0, phoneView.height, phoneView.width, 1)];
    dividingLineView.backgroundColor = SDColor(57, 187, 255);
    [phoneView addSubview:dividingLineView];
    
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    [self LoginClick];
    return YES;
}


- (void)LoginClick
{
    if (self.phoneField.text.length < 11 || self.phoneField.text.length > 11) {
        [MBProgressHUD showError:@"你手机号码输入错误"];
        return;
    }
    
    if (self.pwdField.text.length < 8 ) {
        [MBProgressHUD showError:@"密码输入错误"];
        return;
    }
    
    [MBProgressHUD showSuccess:@"登入成功"];
    
}


@end
