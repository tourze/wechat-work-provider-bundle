# WechatWorkProviderBundle 测试计划

## 测试环境检查

- ✅ composer.json 包含 autoload-dev 配置
- ✅ composer.json 包含 phpunit/phpunit 依赖  
- ⏳ 复制 phpunit.yml 工作流文件
- ⏳ 运行 composer dump-autoload

## 测试用例列表

### 🏢 实体类 (Entity)

#### 📋 AuthCorp Entity

- ✅ `tests/Entity/AuthCorpTest.php`
- 🎯 **关注点**: 实体属性设置、关系映射、字符串转换
- 📝 **场景**:
  - 基本属性设置/获取
  - 关联关系操作
  - __toString() 方法
  - AccessTokenAware 接口实现
- ✅ **测试状态**: 已完成，30个测试，58个断言，全部通过

#### 🏪 Provider Entity  

- ✅ `tests/Entity/ProviderTest.php`
- 🎯 **关注点**: 服务商信息管理、Token管理
- 📝 **场景**:
  - 基本属性操作
  - 时间戳处理
  - 关联关系管理
- ✅ **测试状态**: 已完成，19个测试，63个断言，全部通过

#### 📱 Suite Entity

- ⏳ `tests/Entity/SuiteTest.php`
- 🎯 **关注点**: 代开发应用模板管理
- 📝 **场景**:
  - 模板信息设置
  - Token和Ticket管理
  - 授权企业关联
- ❌ **测试状态**: 未开始

#### 📨 CorpServerMessage Entity

- ⏳ `tests/Entity/CorpServerMessageTest.php`
- 🎯 **关注点**: 代开发回调消息处理
- 📝 **场景**:
  - 消息数据设置
  - 原始数据处理
  - 响应数据设置
- ❌ **测试状态**: 未开始

#### 📬 ProviderServerMessage Entity

- ⏳ `tests/Entity/ProviderServerMessageTest.php`
- 🎯 **关注点**: 服务商回调消息
- 📝 **场景**:
  - 基本消息属性
  - 上下文数据处理
- ❌ **测试状态**: 未开始

#### 📫 SuiteServerMessage Entity  

- ⏳ `tests/Entity/SuiteServerMessageTest.php`
- 🎯 **关注点**: 应用模板回调消息
- 📝 **场景**:
  - 基本消息属性
  - 上下文数据处理
- ❌ **测试状态**: 未开始

### 🔧 服务类 (Service)

#### 🚀 ProviderService

- ✅ `tests/Service/ProviderServiceTest.php`
- 🎯 **关注点**: 核心API服务、Token管理、数据同步
- 📝 **场景**:
  - Token自动刷新机制
  - API请求处理
  - 企业信息同步
  - 异常处理
  - 响应格式化
- ✅ **测试状态**: 已完成，7个测试，35个断言，全部通过

### 🎮 控制器 (Controller)

#### 🌐 ServerController

- ⏳ `tests/Controller/ServerControllerTest.php`
- 🎯 **关注点**: 回调接口处理、消息解密、事件分发
- 📝 **场景**:
  - GET请求验证
  - POST消息解密
  - 不同类型回调处理
  - 异常情况处理
- ❌ **测试状态**: 未开始

#### 🧪 TestController

- ⏳ `tests/Controller/TestControllerTest.php`
- 🎯 **关注点**: 测试接口功能
- 📝 **场景**:
  - 代理获取逻辑
  - 参数处理
- ❌ **测试状态**: 未开始

#### 🧪 TestExternalContactController

- ⏳ `tests/Controller/TestExternalContactControllerTest.php`
- 🎯 **关注点**: 外部联系人测试接口
- 📝 **场景**:
  - 账号激活功能
  - 参数验证
- ❌ **测试状态**: 未开始

### ⚡ 命令 (Command)

#### 🔄 RefreshAuthCorpAccessTokenCommand

- ⏳ `tests/Command/RefreshAuthCorpAccessTokenCommandTest.php`
- 🎯 **关注点**: 定时刷新访问令牌
- 📝 **场景**:
  - 正常刷新流程
  - Token过期处理
  - 错误日志记录
  - 批量处理
- ❌ **测试状态**: 未开始

#### 🔄 SyncWechatWorkCorpInfoCommand  

- ⏳ `tests/Command/SyncWechatWorkCorpInfoCommandTest.php`
- 🎯 **关注点**: 企业信息同步
- 📝 **场景**:
  - 同步流程执行
  - 错误处理
- ❌ **测试状态**: 未开始

### 🎧 事件订阅者 (EventSubscriber)

#### 🏢 AuthCorpListener

- ⏳ `tests/EventSubscriber/AuthCorpListenerTest.php`
- 🎯 **关注点**: 授权企业自动创建
- 📝 **场景**:
  - create_auth 事件处理
  - reset_permanent_code 事件处理
  - API调用和数据保存
- ❌ **测试状态**: 未开始

#### 📱 SuiteListener

- ⏳ `tests/EventSubscriber/SuiteListenerTest.php`
- 🎯 **关注点**: Suite Ticket更新
- 📝 **场景**:
  - suite_ticket 事件处理
  - Ticket更新逻辑
- ❌ **测试状态**: 未开始

#### 🔔 WechatWorkSubscriber

- ⏳ `tests/EventSubscriber/WechatWorkSubscriberTest.php`
- 🎯 **关注点**: 事件转发和同步
- 📝 **场景**:
  - 消息响应事件处理
  - 数据同步逻辑
- ❌ **测试状态**: 未开始

### 🔐 遗留API (LegacyApi)

#### 🔒 WXBizMsgCrypt

- ✅ `tests/LegacyApi/WXBizMsgCryptTest.php`
- 🎯 **关注点**: 消息加解密核心功能
- 📝 **场景**:
  - URL验证
  - 消息加密
  - 消息解密
  - 错误码处理
  - 边界值测试
- ✅ **测试状态**: 已完成，14个测试，66个断言，全部通过

#### 🔒 Prpcrypt

- ⏳ `tests/LegacyApi/PrpcryptTest.php`
- 🎯 **关注点**: AES加解密实现
- 📝 **场景**:
  - 加密功能
  - 解密功能
  - 错误处理
  - 边界测试
- ❌ **测试状态**: 未开始

#### 🔒 PKCS7Encoder

- ✅ `tests/LegacyApi/PKCS7EncoderTest.php`
- 🎯 **关注点**: PKCS7填充算法
- 📝 **场景**:
  - 编码功能
  - 解码功能
  - 边界情况
- ✅ **测试状态**: 已完成，17个测试，95个断言，全部通过

#### 🔒 SHA1

- ⏳ `tests/LegacyApi/SHA1Test.php`
- 🎯 **关注点**: SHA1签名计算
- 📝 **场景**:
  - 正常签名计算
  - 参数排序
  - 异常处理
- ❌ **测试状态**: 未开始

#### 🔒 XMLParse

- ⏳ `tests/LegacyApi/XMLParseTest.php`
- 🎯 **关注点**: XML消息解析和生成
- 📝 **场景**:
  - XML提取功能
  - XML生成功能
  - 异常处理
- ❌ **测试状态**: 未开始

### 📡 请求类 (Request)

#### 🔑 GetCorpTokenRequest

- ✅ `tests/Request/GetCorpTokenRequestTest.php`
- 🎯 **关注点**: 企业Token获取请求
- 📝 **场景**:
  - 请求参数设置
  - 请求路径验证
  - 请求选项构建
- ✅ **测试状态**: 已完成，11个测试，21个断言，全部通过

#### 🔑 GetProviderTokenRequest

- ⏳ `tests/Request/GetProviderTokenRequestTest.php`
- 🎯 **关注点**: 服务商Token获取
- 📝 **场景**:
  - 参数设置验证
  - 请求构建
- ❌ **测试状态**: 未开始

#### 🔑 GetSuiteTokenRequest

- ⏳ `tests/Request/GetSuiteTokenRequestTest.php`
- 🎯 **关注点**: 套件Token获取
- 📝 **场景**:
  - 参数验证
  - 请求构建
- ❌ **测试状态**: 未开始

#### 🔐 GetPermanentCodeRequest

- ⏳ `tests/Request/GetPermanentCodeRequestTest.php`
- 🎯 **关注点**: 永久授权码获取
- 📝 **场景**:
  - 授权码参数
  - 请求构建
- ❌ **测试状态**: 未开始

### 🗃️ 存储库 (Repository)

#### 📊 AuthCorpRepository

- ⏳ `tests/Repository/AuthCorpRepositoryTest.php`
- 🎯 **关注点**: 授权企业查询功能
- 📝 **场景**:
  - getAgentByAuthCorp 方法
  - 基本查询功能
- ❌ **测试状态**: 未开始

### 📅 事件类 (Event)

#### 📢 CorpServerMessageResponseEvent

- ⏳ `tests/Event/CorpServerMessageResponseEventTest.php`
- 🎯 **关注点**: 事件数据传递
- 📝 **场景**:
  - 消息设置获取
  - 授权企业设置获取
- ❌ **测试状态**: 未开始

## 测试执行计划

### 第一阶段: 基础设施 ⏳

1. 复制 phpunit.yml 工作流文件
2. 运行 composer dump-autoload
3. 测试基础实体类

### 第二阶段: 核心功能 ⏳

1. 测试 LegacyApi 加解密功能
2. 测试 ProviderService 核心服务
3. 测试主要的 Request 类

### 第三阶段: 应用层 ⏳

1. 测试控制器
2. 测试命令
3. 测试事件订阅者

### 第四阶段: 完整性测试 ⏳

1. 集成测试
2. 边界测试
3. 异常测试

## 测试覆盖率目标

- 🎯 代码覆盖率: > 90%
- 🎯 分支覆盖率: > 85%
- 🎯 异常场景覆盖: 100%

## 已知挑战

- 🚨 需要模拟外部API调用
- 🚨 需要模拟Doctrine实体管理器
- 🚨 需要处理加密解密的复杂逻辑
- 🚨 需要模拟HTTP请求和响应

## 🚨 发现的代码问题

### ✅ AuthCorp 实体类型不一致问题 - 已解决

**问题描述**:
在 `src/Entity/AuthCorp.php` 中发现类型声明不一致的问题：

1. 属性 `authInfo`, `authUserInfo`, `dealerCorpInfo`, `registerCodeInfo` 的类型声明为 `array`
2. 对应的 setter 方法参数类型是 `?array` (允许null)
3. 当调用 setter 传入 null 值时，会报 TypeError

**解决方案**: 
采用选项 2，修改 setter 方法处理 null 值，使用 `$this->property = $value ?? [];` 
确保类型安全的同时保持向后兼容性。

✅ **问题已修复**: 所有相关测试通过

## 📊 测试进度总结

### 已完成测试用例
- ✅ AuthCorp Entity: 30个测试，58个断言
- ✅ Provider Entity: 19个测试，63个断言  
- ✅ GetCorpTokenRequest: 11个测试，21个断言
- ✅ ProviderService: 7个测试，35个断言
- ✅ WXBizMsgCrypt: 14个测试，66个断言
- ✅ PKCS7Encoder: 17个测试，95个断言

**总计**: 98个测试，338个断言，全部通过 ✅

### 剩余待测试项目
- ⏳ Suite Entity
- ⏳ 其他 ServerMessage 实体
- ⏳ 其他 Request 类
- ⏳ Controller 控制器
- ⏳ Command 命令
- ⏳ EventSubscriber 事件订阅者
- ⏳ 其他 LegacyApi 类
