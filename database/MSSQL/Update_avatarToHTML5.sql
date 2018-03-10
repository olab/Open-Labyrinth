USE [openlabyrinth]
DROP TABLE [dbo].[map_avatars];

/****** Object:  Table [dbo].[map_avatars]    Script Date: 04/03/2012 18:40:53 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[map_avatars]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[map_avatars](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[map_id] [bigint] NOT NULL,
	[skin_1] [nvarchar](6) NULL,
	[skin_2] [nvarchar](6) NULL,
	[cloth] [nvarchar](6) NULL,
	[nose] [nvarchar](20) NULL,
	[hair] [nvarchar](20) NULL,
	[environment] [nvarchar](20) NULL,
	[accessory_1] [nvarchar](20) NULL,
	[bkd] [nvarchar](6) NULL,
	[sex] [nvarchar](20) NULL,
	[mouth] [nvarchar](20) NULL,
	[outfit] [nvarchar](20) NULL,
	[bubble] [nvarchar](20) NULL,
	[bubble_text] [nvarchar](100) NULL,
	[accessory_2] [nvarchar](20) NULL,
	[accessory_3] [nvarchar](20) NULL,
	[age] [nvarchar](2) NULL,
	[eyes] [nvarchar](20) NULL,
	[hair_color] [nvarchar](6) NULL,
	[image] [nvarchar](100) NULL,
 CONSTRAINT [PK_map_avatars] PRIMARY KEY CLUSTERED
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO