//
//  OTT6PublicHeader.h
//  OTT6IOS
//
//  Created by sunda on 15/8/13.
//  Copyright (c) 2015年 OTT6IOS. All rights reserved.
//

//这里是我写的一些公共的方法

#ifdef DEBUG // 调试状态, 打开LOG功能
#define SDLog(...) NSLog(__VA_ARGS__)
#else // 发布状态, 关闭LOG功能
#define SDLog(...)
#endif


/** 随机色 */
#define SDRandomColor [UIColor colorWithRed:arc4random_uniform(256)/255.0 green:arc4random_uniform(256)/255.0 blue:arc4random_uniform(256)/255.0 alpha:1.0]

/** 颜色 */
#define SDColor(r, g, b) [UIColor colorWithRed:(r)/255.0 green:(g)/255.0 blue:(b)/255.0 alpha:1.0]

#define SDFrame(x,y,w,h) [Device X:x Y:y width:w height:h]

#define SDSize(w,h) [Device width:w height:h];

#define SDScaleX [Device getViewScale].x

#define SDScaleY [Device getViewScale].x
