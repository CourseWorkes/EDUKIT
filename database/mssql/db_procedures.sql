USE DBC;
GO

CREATE FUNCTION existTable (
	@table varchar(255)
) RETURNS TINYINT
AS
BEGIN
	DECLARE @result TINYINT;

	IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME=@table)
	BEGIN
		SET @result = 1;
	END
	ELSE 
	BEGIN
		SET @result = 0;
	END;

	RETURN @result;
END;
GO
	

